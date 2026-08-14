<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PakasirService;
use App\Models\Tagihan;

use App\Models\Siswa;
use App\Services\TagihanService;

class PembayaranController extends Controller
{
    public function index()
    {
        $siswa = Siswa::where('user_id', auth()->id())->first();

        if (!$siswa) {
            return view('user.pembayaran.index', ['tagihans' => [], 'riwayat' => []]);
        }

        // Tagihan yang harus dibayar (Muncul di Card atas)
        $tagihans = Tagihan::where('siswa_id', $siswa->id)
            ->where('status', 'belum')
            ->get();

        // Tagihan yang sudah lunas (Muncul di Tabel bawah)
        $riwayat = Tagihan::where('siswa_id', $siswa->id)
            ->where('status', 'lunas')
            ->get();

        return view('user.pembayaran.index', compact('tagihans', 'riwayat', 'siswa'));
    }

    public function pay(Request $request, $id, PakasirService $pakasir)
    {
        try {
            $tagihan = Tagihan::with('siswa.user')->findOrFail($id);

            // Buat Order ID Baru (Format: SPP-{tagihan_id}-{timestamp})
            $orderId = 'SPP-' . $tagihan->id . '-' . time();

            // Biarkan pengguna memilih metode pembayaran di halaman Pakasir
            $method = null;
            $adminFee = 0;

            // Dapatkan URL Pembayaran dari Pakasir
            $paymentUrl = $pakasir->createPaymentUrl($tagihan, $orderId, $method, $adminFee);

            // Kembalikan redirect_url ke frontend
            return response()->json([
                'redirect_url' => $paymentUrl
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function webhook(Request $request, TagihanService $tagihanService)
    {
        $payload = $request->all();
        \Log::info('Webhook Pakasir Masuk:', $payload);

        $orderId = $payload['order_id'] ?? null;
        $status = $payload['status'] ?? $payload['transaction_status'] ?? null;
        
        // Verifikasi sederhana bahwa webhook ditujukan untuk project kita
        if (($payload['project'] ?? null) !== config('pakasir.project_slug')) {
            \Log::warning('Webhook Pakasir: Invalid Project Slug', ['project' => $payload['project'] ?? null]);
            // Return 200 supaya Pakasir tidak retry terus menerus, tapi kita abaikan
            return response()->json(['status' => 'ignored']);
        }

        // Ekstrak tagihan_id dari order_id (Format: SPP-{tagihan_id}-{timestamp})
        if ($orderId) {
            $parts = explode('-', $orderId);
            $tagihanId = $parts[1] ?? null;

            if ($tagihanId) {
                $tagihan = Tagihan::find($tagihanId);

                if ($tagihan) {
                    \Log::info('Webhook Pakasir: Tagihan ditemukan', ['tagihan_id' => $tagihan->id, 'status_webhook' => $status]);
                    
                    // Jika status pembayaran berhasil (Pakasir mengirimkan 'completed')
                    if (in_array(strtolower($status), ['completed', 'success', 'paid', 'settlement', 'berhasil'])) {
                        if ($tagihan->status !== 'lunas') {
                            // Sesuai dokumen Pakasir, metode ada di key 'payment_method'
                            $metode = $payload['payment_method'] ?? 'Pakasir';

                            $tagihan->update([
                                'status' => 'lunas',
                                'metode' => $metode,
                                'tanggal_bayar' => now()
                            ]);

                            $tagihanService->markAsPaid($tagihan);
                            \Log::info('Webhook Pakasir: Tagihan berhasil dilunasi');
                        } else {
                            \Log::info('Webhook Pakasir: Tagihan sudah lunas sebelumnya');
                        }
                    } else {
                        \Log::warning('Webhook Pakasir: Status tidak dikenali sebagai sukses', ['status' => $status]);
                    }
                } else {
                    \Log::warning('Webhook Pakasir: Tagihan tidak ditemukan', ['tagihan_id' => $tagihanId]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MidtransService;
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

    public function pay($id, MidtransService $midtrans)
    {
        try {
            $tagihan = Tagihan::with('siswa.user')->findOrFail($id);

            // Buat Order ID Baru (Format: SPP-{tagihan_id}-{timestamp})
            $orderId = 'SPP-' . $tagihan->id . '-' . time();

            // Minta data Snap Token baru ke Midtrans
            $snapToken = $midtrans->createTransaction($tagihan, $orderId);

            // Kembalikan snap_token ke frontend agar JavaScript bisa memicu pop-up
            return response()->json([
                'snap_token' => $snapToken
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function webhook(Request $request, TagihanService $tagihanService)
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;

        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($expectedSignature !== $signatureKey) {
            return response()->json(['message' => 'Verifikasi Gagal'], 403);
        }

        // Ekstrak tagihan_id dari order_id (Format: SPP-{tagihan_id}-{timestamp})
        if ($orderId) {
            $parts = explode('-', $orderId);
            $tagihanId = $parts[1] ?? null;

            if ($tagihanId) {
                $tagihan = Tagihan::find($tagihanId);

                if ($tagihan) {
                    if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                        if ($tagihan->status !== 'lunas') {
                            $metode = $payload['payment_type'] ?? 'Midtrans';

                            // Ekstrak nama bank atau gerai retail (Alfamart/Indomaret)
                            if ($metode === 'bank_transfer') {
                                if (isset($payload['va_numbers'][0]['bank'])) {
                                    $metode = $payload['va_numbers'][0]['bank'];
                                } elseif (isset($payload['permata_va_number'])) {
                                    $metode = 'permata';
                                }
                            } elseif ($metode === 'echannel') {
                                $metode = 'mandiri'; // Mandiri Bill
                            } elseif ($metode === 'cstore') {
                                if (isset($payload['store'])) {
                                    $metode = $payload['store']; // alfamart atau indomaret
                                }
                            }

                            $tagihan->update([
                                'status' => 'lunas',
                                'metode' => $metode,
                                'tanggal_bayar' => now()
                            ]);

                            $tagihanService->markAsPaid($tagihan);
                        }
                    }
                    // Jika gagal/cancel/expire, kita abaikan saja karena tidak ada state 'failed' di Tagihan.
                    // Status Tagihan tetap 'belum'.
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
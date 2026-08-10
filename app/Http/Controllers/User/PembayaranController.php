<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MidtransService;
use App\Models\Tagihan;
use App\Models\Pembayaran;
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
        $riwayat = Tagihan::with('pembayaran')
            ->where('siswa_id', $siswa->id)
            ->where('status', 'lunas')
            ->get();

        return view('user.pembayaran.index', compact('tagihans', 'riwayat', 'siswa'));
    }

    public function pay($id, MidtransService $midtrans)
    {
        try {
            $tagihan = Tagihan::with('siswa.user')->findOrFail($id);

            // Cari data pembayaran yang statusnya masih pending
            $pembayaran = Pembayaran::where('tagihan_id', $tagihan->id)
                ->where('status', 'pending')
                ->first();

            // 1. KITA SELALU BUAT ORDER ID BARU
            $orderId = 'SPP-' . $tagihan->id . '-' . time();

            if (!$pembayaran) {
                $pembayaran = Pembayaran::create([
                    'tagihan_id'        => $tagihan->id,
                    'midtrans_order_id' => $orderId,
                    'jumlah'            => $tagihan->jumlah,
                    'metode'            => 'pending',
                    'status'            => 'pending',
                    'tanggal_bayar'     => now(),
                ]);
            } else {
                // 2. Jika sudah ada data pending, timpa Order ID-nya dengan yang baru
                $pembayaran->update([
                    'midtrans_order_id' => $orderId,
                    'snap_token' => null // Kosongkan dulu token lamanya
                ]);
            }

           // 3. Minta data Snap Token baru ke Midtrans
            $snapToken = $midtrans->createTransaction($tagihan, $orderId);

            // 4. Simpan snap_token terbaru ke database (sebagai cadangan)
            $pembayaran->update(['snap_token' => $snapToken]);

            // 5. Kembalikan snap_token ke frontend agar JavaScript bisa memicu pop-up
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

        $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->first();

        if ($pembayaran) {
            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                if ($pembayaran->status !== 'paid') {
                    $pembayaran->update([
                        'status' => 'paid',
                        'metode' => $payload['payment_type'] ?? 'Midtrans',
                        'tanggal_bayar' => now()
                    ]);

                    $tagihan = $pembayaran->tagihan;
                    if ($tagihan) {
                        $tagihanService->markAsPaid($tagihan);
                    }
                }
            } else if ($transactionStatus === 'cancel' || $transactionStatus === 'deny' || $transactionStatus === 'expire') {
                $pembayaran->update([
                    'status' => 'failed',
                    'snap_token' => null
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
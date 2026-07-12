<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DokuService;
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

        return view('user.pembayaran.index', compact('tagihans', 'riwayat'));
    }

    public function pay($id, DokuService $doku)
    {
        try {
            $tagihan = Tagihan::with('siswa.user')->findOrFail($id);

            // Cari data pembayaran yang statusnya masih pending
            $pembayaran = Pembayaran::where('tagihan_id', $tagihan->id)
                ->where('status', 'pending')
                ->first();

            // 1. KITA SELALU BUAT ORDER ID BARU
            // Supaya Doku menganggap ini permintaan baru & bisa ganti metode
            $orderId = 'SPP-' . $tagihan->id . '-' . time();

            if (!$pembayaran) {
                $pembayaran = Pembayaran::create([
                    'tagihan_id'        => $tagihan->id,
                    'doku_order_id'     => $orderId,
                    'jumlah'            => $tagihan->jumlah,
                    'metode'            => 'pending',
                    'status'            => 'pending',
                    'tanggal_bayar'     => now(),
                ]);
            } else {
                // 2. Jika sudah ada data pending, timpa Order ID-nya dengan yang baru
                $pembayaran->update([
                    'doku_order_id' => $orderId,
                    'payment_url' => null // Kosongkan dulu URL lamanya
                ]);
            }

            // 3. Minta URL Checkout baru ke Doku dengan Order ID yang baru
            $paymentUrl = $doku->createTransaction($tagihan, $orderId);

            // 4. Simpan payment_url terbaru ke database
            $pembayaran->update(['payment_url' => $paymentUrl]);

            return response()->json(['payment_url' => $paymentUrl]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function webhook(Request $request, TagihanService $tagihanService)
    {
        $clientId = $request->header('Client-Id');
        $requestId = $request->header('Request-Id');
        $timestamp = $request->header('Request-Timestamp');
        $signature = $request->header('Signature');

        if (!$clientId || !$requestId || !$timestamp || !$signature) {
            return response()->json(['message' => 'Invalid Headers'], 400);
        }

        $jsonPayload = $request->getContent();
        $digest = base64_encode(hash('sha256', $jsonPayload, true));
        $signatureString = "Client-Id:" . config('doku.client_id') . "\n" .
                           "Request-Id:" . $requestId . "\n" .
                           "Request-Timestamp:" . $timestamp . "\n" .
                           "Request-Target:" . $request->getRequestUri() . "\n" .
                           "Digest:" . $digest;

        $expectedSignature = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $signatureString, config('doku.secret_key'), true));

        if ($signature !== $expectedSignature) {
            return response()->json(['message' => 'Verifikasi Gagal'], 403);
        }

        $transactionStatus = $request->input('transaction.status');
        $orderId = $request->input('order.invoice_number');

        $pembayaran = Pembayaran::where('doku_order_id', $orderId)->first();

        if ($pembayaran) {
            if ($transactionStatus === 'SUCCESS') {
                if ($pembayaran->status !== 'paid') {
                    $pembayaran->update([
                        'status' => 'paid',
                        'metode' => $request->input('transaction.payment_method', 'DOKU'),
                        'tanggal_bayar' => now()
                    ]);

                    $tagihan = $pembayaran->tagihan;
                    if ($tagihan) {
                        $tagihanService->markAsPaid($tagihan);
                    }
                }
            } else if ($transactionStatus === 'FAILED' || $transactionStatus === 'EXPIRED') {
                $pembayaran->update([
                    'status' => 'failed',
                    'payment_url' => null
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
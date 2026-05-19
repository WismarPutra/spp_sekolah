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

        return view('user.pembayaran.index', compact('tagihans', 'riwayat'));
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
            // Supaya Midtrans menganggap ini permintaan baru & bisa ganti metode
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

            // 3. Minta token baru ke Midtrans dengan Order ID yang baru
            $snapToken = $midtrans->createTransaction($tagihan, $orderId);

            // 4. Simpan snap_token terbaru ke database
            $pembayaran->update(['snap_token' => $snapToken]);

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            // ... (Logika penanganan error 400/407 Anda tetap dipertahankan) ...
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function webhook(Request $request, TagihanService $tagihanService)
    {
        try {
            // SDK melakukan verifikasi signature secara otomatis
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Verifikasi Gagal'], 403);
        }

        $transaction = $notif->transaction_status;
        $order_id = $notif->order_id;

        $pembayaran = Pembayaran::where('midtrans_order_id', $order_id)->first();

        if ($pembayaran) {
            if (in_array($transaction, ['settlement', 'capture'])) {
                if ($pembayaran->status !== 'paid') {
                    $pembayaran->update([
                        'status' => 'paid',
                        'metode' => $notif->payment_type,
                        'tanggal_bayar' => now()
                    ]);

                    $tagihan = $pembayaran->tagihan;
                    if ($tagihan) {
                        $tagihanService->markAsPaid($tagihan);
                    }
                }
            } else if (in_array($transaction, ['kadaluwarsa', 'failure', 'cancel'])) {
                $pembayaran->update([
                    'status' => 'failed', // atau 'expired' sesuai kebutuhan skripsimu
                    'snap_token' => null
                ]);

                // Logika tambahan: Tagihan tetap 'unpaid', snap_token bisa di-null-kan
                // agar wali murid bisa melakukan checkout ulang.
            }
        }
        return response()->json(['status' => 'success']);
    }
}
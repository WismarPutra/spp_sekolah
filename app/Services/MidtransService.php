<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Exception;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction($tagihan, $orderId, $paymentMethod = null, $adminFee = 0)
    {
        $grossAmount = (int) $tagihan->jumlah + $adminFee;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $tagihan->siswa->nama ?? 'Siswa',
                'email' => $tagihan->siswa->user->email ?? auth()->user()->email,
            ]
        ];

        // Paksa agar Midtrans hanya memunculkan metode bayar yang dipilih user
        if ($paymentMethod) {
            $params['enabled_payments'] = [$paymentMethod];
        }

        // Rincian Pembayaran
        $itemDetails = [
            [
                'id' => 'SPP-' . $tagihan->id,
                'price' => (int) $tagihan->jumlah,
                'quantity' => 1,
                'name' => 'SPP Bulan ' . $tagihan->bulan_text . ' ' . $tagihan->tahun
            ]
        ];

        if ($adminFee > 0) {
            $itemDetails[] = [
                'id' => 'ADMIN-FEE',
                'price' => (int) $adminFee,
                'quantity' => 1,
                'name' => 'Biaya Admin/Layanan'
            ];
        }

        $params['item_details'] = $itemDetails;

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (Exception $e) {
            throw new Exception('Midtrans API Error: ' . $e->getMessage());
        }
    }
}

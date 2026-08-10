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

    public function createTransaction($tagihan, $orderId)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $tagihan->jumlah,
            ],
            'customer_details' => [
                'first_name' => $tagihan->siswa->nama_siswa ?? 'Siswa',
                'email' => $tagihan->siswa->user->email ?? auth()->user()->email,
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (Exception $e) {
            throw new Exception('Midtrans API Error: ' . $e->getMessage());
        }
    }
}

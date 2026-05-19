<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;

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
                'order_id'     => $orderId,
                'gross_amount' => (int) $tagihan->jumlah,
            ],
            'customer_details' => [
                'first_name' => $tagihan->siswa->nama_siswa ?? 'Siswa',
                'email'      => $tagihan->siswa->user->email ?? auth()->user()->email,
            ],
            'enabled_payments' => ['bri_va', 'indomaret', 'alfamart', 'qris', 'gopay', 'shopeepay'],
        ];

        return \Midtrans\Snap::getSnapToken($params);
    }
}
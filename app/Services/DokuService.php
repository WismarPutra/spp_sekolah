<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuService
{
    protected $clientId;
    protected $secretKey;
    protected $isProduction;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('doku.client_id');
        $this->secretKey = config('doku.secret_key');
        $this->isProduction = config('doku.is_production');
        $this->baseUrl = $this->isProduction 
            ? 'https://api.doku.com' 
            : 'https://api-sandbox.doku.com';
    }

    public function createTransaction($tagihan, $orderId)
    {
        $path = '/checkout/v1/payment';
        $url = $this->baseUrl . $path;

        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        $payload = [
            'order' => [
                'amount' => (int) $tagihan->jumlah,
                'invoice_number' => $orderId,
            ],
            'payment' => [
                'payment_due_date' => 60 // 60 menit
            ],
            'customer' => [
                'name' => $tagihan->siswa->nama_siswa ?? 'Siswa',
                'email' => $tagihan->siswa->user->email ?? auth()->user()->email,
            ]
        ];

        $jsonPayload = json_encode($payload);
        $digest = base64_encode(hash('sha256', $jsonPayload, true));

        $signatureString = "Client-Id:" . $this->clientId . "\n" .
                           "Request-Id:" . $requestId . "\n" .
                           "Request-Timestamp:" . $timestamp . "\n" .
                           "Request-Target:" . $path . "\n" .
                           "Digest:" . $digest;

        $signature = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $signatureString, $this->secretKey, true));

        $response = Http::withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
            'Content-Type' => 'application/json'
        ])->post($url, $payload);

        if ($response->successful()) {
            return $response->json('response.payment.url');
        }

        throw new \Exception('Doku API Error: ' . $response->body());
    }
}

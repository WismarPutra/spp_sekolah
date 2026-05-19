<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function send($phone, $message)
    {
        $token = config('services.fonnte.token');

        try {
            sleep(5); // anti spam tambahan

            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $this->format($phone),
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->failed()) {
                \Log::error('Gagal kirim WA', [
                    'phone' => $phone,
                    'response' => $response->body()
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            \Log::error('Error WA Service', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function format($no)
    {
        $no = preg_replace('/[^0-9]/', '', $no);

        if (substr($no, 0, 2) === '62') {
            return $no;
        }

        return '62' . ltrim($no, '0');
    }
}
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function send($phone, $message, $isRetry = false)
    {
        $token = config('services.fonnte.token');

        try {
            sleep(5); // Jeda aman 5 detik agar worker bisa memproses lebih banyak job dalam batas waktu max-time 45 detik

            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $this->format($phone),
                'message' => $message,
                'countryCode' => '62',
            ]);

            $responseData = $response->json();

            // Cek jika request HTTP gagal atau respons dari fonnte API false (misal device disconnect)
            if ($response->failed() || (isset($responseData['status']) && $responseData['status'] === false)) {
                \Log::error('Gagal kirim WA', [
                    'phone' => $phone,
                    'response' => $responseData ?? $response->body()
                ]);

                if ($isRetry) {
                    // Jika ini dipanggil dari Job retry, lemparkan exception agar Laravel melakukan retry ulang sesuai backoff job
                    throw new \Exception('Fonnte gagal: ' . json_encode($responseData ?? $response->body()));
                } else {
                    // Jika dipanggil pertama kali, lemparkan ke queue/job untuk dicoba lagi nanti
                    \App\Jobs\RetryWhatsAppMessageJob::dispatch($phone, $message)->delay(now()->addMinutes(1));
                }
            }

            return $response;
        } catch (\Exception $e) {
            \Log::error('Error WA Service', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            if ($isRetry) {
                // Lempar kembali exception agar Laravel Queue meretry ulang Job ini
                throw $e;
            } else {
                // Masukkan antrean retry jika ada Exception (misal timeout, jaringan mati)
                \App\Jobs\RetryWhatsAppMessageJob::dispatch($phone, $message)->delay(now()->addMinutes(1));
            }
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
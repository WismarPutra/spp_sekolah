<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class PakasirService
{
    protected $apiUrl;
    protected $apiKey;
    protected $projectSlug;

    public function __construct()
    {
        $this->apiKey = config('pakasir.api_key');
        $this->projectSlug = config('pakasir.project_slug');
        $this->apiUrl = config('pakasir.is_production') 
            ? 'https://app.pakasir.com/api' 
            : 'https://app.pakasir.com/api'; 
    }

    public function createPaymentUrl($tagihan, $orderId, $method = null, $adminFee = 0)
    {
        $total = $tagihan->jumlah + $adminFee;
        
        // Pakasir mendukung pembuatan payment link secara langsung tanpa HTTP Request POST
        // Format: https://app.pakasir.com/pay/{slug}/{amount}?order_id={order_id}
        
        $paymentUrl = "https://app.pakasir.com/pay/{$this->projectSlug}/{$total}?order_id={$orderId}";
        
        return $paymentUrl;
    }
}

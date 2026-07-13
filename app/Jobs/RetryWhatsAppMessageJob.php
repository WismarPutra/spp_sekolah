<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\WhatsAppService;

class RetryWhatsAppMessageJob implements ShouldQueue
{
    use Queueable;

    protected $phone;
    protected $message;

    // Retry configuration
    public $tries = 10;
    public $backoff = [60, 300, 600, 1800, 3600]; // 1m, 5m, 10m, 30m, 1h

    /**
     * Create a new job instance.
     */
    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $wa): void
    {
        // isRetry = true so it throws exception on fail instead of dispatching another job
        $wa->send($this->phone, $this->message, true);
    }
}

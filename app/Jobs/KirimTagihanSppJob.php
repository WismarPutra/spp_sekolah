<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\WhatsAppService;

class KirimTagihanSppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Batasi percobaan hanya 1 kali untuk mencegah double
    public $tries = 1;
    // Beri waktu timeout yang masuk akal
    public $timeout = 30;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $wa = app(WhatsAppService::class);
        $wa->send($this->data['no_hp'], $this->data['pesan']);
    }
}
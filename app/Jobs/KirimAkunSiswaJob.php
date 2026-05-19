<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class KirimAkunSiswaJob implements ShouldQueue
{
    use Queueable;

    protected $no_hp;
    protected $nama;
    protected $email;
    protected $password;

    public function __construct($no_hp, $nama, $email, $password)
    {
        $this->no_hp = $no_hp;
        $this->nama = $nama;
        $this->email = $email;
        $this->password = $password;
    }

    public function handle(WhatsAppService $wa)
    {
        // delay biar tidak ke-block
        sleep(5);

        $message =
            "Halo {$this->nama},
            Akun kamu sudah dibuat.
            Email: {$this->email}
            Password: {$this->password}";

        $wa->send($this->no_hp, $message);
    }
}
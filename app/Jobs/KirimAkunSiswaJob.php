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

        $link = "https://smkutamaciranjang.sch.id/";

        $message = "Kepada Yth. Orang Tua/Wali Murid *{$this->nama}*,

        Dengan hormat,

        Kami informasikan bahwa akun Bapak/Ibu telah berhasil dibuat/diperbarui. Berikut adalah informasi akun yang dapat digunakan untuk login ke sistem SPP SMK Utama Cianjur:

        *Email:* {$this->email}
        *Password:* {$this->password}

        Silakan klik link di bawah ini untuk menuju halaman login:
        https://smkutamaciranjang.sch.id/

        Mohon untuk menyimpan informasi akun tersebut dengan baik dan tidak membagikannya kepada pihak lain demi menjaga keamanan akun.

        Atas perhatian dan kerja samanya, kami ucapkan terima kasih.

        *Hormat kami,*
        *Petugas Tata Usaha SMK Utama Cianjur*";

        $wa->send($this->no_hp, $message);
    }
}
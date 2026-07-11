<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $fillable = [
        'siswa_id',
        'user_id',
        'spp_id',
        'bulan',
        'tahun',
        'jumlah',
        'status',
        'reminder_sent'
    ];


    public function siswa()
    {
        // Tagihan dimiliki oleh satu siswa
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function pembayaran()
    {
        // Asumsi: satu tagihan memiliki satu catatan pembayaran
        return $this->hasOne(Pembayaran::class, 'tagihan_id');
    }

    public function spp()
    {
        return $this->belongsTo(Spp::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getBulanTextAttribute()
    {
        $listBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return $listBulan[(int)$this->bulan] ?? $this->bulan;
    }
}
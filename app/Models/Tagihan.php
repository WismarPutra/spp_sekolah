<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $fillable = [
        'siswa_nis',
        'bulan',
        'tahun',
        'jumlah',
        'status',
        'reminder_sent',
        'metode',
        'tanggal_bayar'
    ];


    public function siswa()
    {
        // Tagihan dimiliki oleh satu siswa
        return $this->belongsTo(Siswa::class, 'siswa_nis', 'nis');
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

    public function getMetodeAttribute($value)
    {
        if (!$value) {
            return 'Online';
        }

        $val = strtoupper($value);
        if (in_array($val, ['BCA', 'BNI', 'BRI', 'BSI', 'MANDIRI', 'PERMATA', 'QRIS', 'GOPAY', 'SHOPEEPAY'])) {
            return $val;
        }
        
        return ucwords(str_replace('_', ' ', $value));
    }
}
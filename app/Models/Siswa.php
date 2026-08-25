<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'spp_id',
        'nis',
        'nama',
        'kelas',
        'alamat',
        'jurusan',
        'no_hp',
        'tahun_masuk'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function spp()
    {
        return $this->belongsTo(Spp::class, 'spp_id');
    }

    public function tagihans()
    {
        // Siswa memiliki banyak tagihan
        return $this->hasMany(Tagihan::class, 'siswa_nis', 'nis');
    }

    protected static function booted()
    {
        static::deleting(function ($siswa) {
            if ($siswa->user) {
                $siswa->user->delete();
            }
        });
    }
}
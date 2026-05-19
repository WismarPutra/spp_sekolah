<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $fillable = [
        'user_id',
        'nis',
        'nama',
        'kelas',
        'alamat',
        'jurusan',
        'no_hp',
        'is_sent',
        'tahun_masuk'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tagihans()
    {
        // Siswa memiliki banyak tagihan
        return $this->hasMany(Tagihan::class, 'siswa_id');
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
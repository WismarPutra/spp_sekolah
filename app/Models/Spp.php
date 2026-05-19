<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    protected $table = 'spp';

    protected $fillable = [
        'tahun',
        'kelas',
        'jurusan',
        'nominal'
    ];
}

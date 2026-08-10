<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $fillable = [
        'tagihan_id',
        'metode',
        'midtrans_order_id',
        'snap_token',
        'jumlah',
        'tanggal_bayar',
        'status'
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
}
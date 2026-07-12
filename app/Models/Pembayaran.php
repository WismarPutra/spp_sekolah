<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $fillable = [
        'tagihan_id',
        'metode',
        'doku_order_id',
        'payment_url',
        'jumlah',
        'tanggal_bayar',
        'status'
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
}
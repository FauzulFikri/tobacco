<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'no_transaksi',
        'user_id',
        'subtotal',
        'diskon_persen',
        'metode_pembayaran',
        'total',
        'bayar',
        'kembalian',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}

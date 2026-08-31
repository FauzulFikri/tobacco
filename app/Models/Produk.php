<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'gambar',
        'kategori_id',
        'harga',
        'stok',
        'stok_minimum',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function scopeStokMenipis($query)
    {
        return $query->whereColumn('stok', '<=', 'stok_minimum');
    }
}

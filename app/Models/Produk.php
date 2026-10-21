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

    public function stokMutasis()
    {
        return $this->hasMany(StokMutasi::class);
    }

    public function scopeStokMenipis($query)
    {
        return $query->whereColumn('stok', '<=', 'stok_minimum');
    }

    /**
     * Format daftar produk untuk pop-up peringatan stok (dipakai di layout & flash session).
     */
    public static function untukNotifikasi(iterable $produks): array
    {
        return collect($produks)->map(fn (Produk $p) => [
            'nama' => $p->nama_produk,
            'kode' => $p->kode_produk,
            'stok' => (int) $p->stok,
            'stok_minimum' => (int) $p->stok_minimum,
        ])->values()->all();
    }

    /**
     * Dari daftar id produk, ambil yang stoknya sekarang sudah di bawah/sama dengan minimum.
     */
    public static function yangMenipis(array $ids): array
    {
        return self::untukNotifikasi(
            self::whereIn('id', $ids)->stokMenipis()->orderBy('stok')->get()
        );
    }
}

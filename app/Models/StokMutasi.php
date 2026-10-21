<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokMutasi extends Model
{
    protected $fillable = [
        'produk_id',
        'nama_produk',
        'jenis',
        'sumber',
        'qty',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
        'transaksi_id',
        'user_id',
    ];

    /** Label sumber mutasi yang tampil di layar. */
    public const SUMBER_MASUK = [
        'pembelian' => 'Pembelian / Restok',
        'retur_pelanggan' => 'Retur dari Pelanggan',
        'penyesuaian' => 'Penyesuaian (Stok Opname)',
    ];

    public const SUMBER_KELUAR = [
        'rusak' => 'Barang Rusak / Kadaluarsa',
        'hilang' => 'Barang Hilang',
        'retur_supplier' => 'Retur ke Supplier',
        'penyesuaian' => 'Penyesuaian (Stok Opname)',
    ];

    public const SUMBER_SISTEM = [
        'stok_awal' => 'Stok Awal',
        'penjualan' => 'Penjualan',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSumberLabelAttribute(): string
    {
        return self::SUMBER_SISTEM[$this->sumber]
            ?? ($this->jenis === 'masuk' ? self::SUMBER_MASUK : self::SUMBER_KELUAR)[$this->sumber]
            ?? ucfirst(str_replace('_', ' ', $this->sumber));
    }

    /**
     * Ubah stok produk sekaligus mencatat mutasinya.
     * Dipanggil di dalam DB::transaction agar stok & riwayat selalu konsisten.
     */
    public static function catat(
        Produk $produk,
        string $jenis,
        int $qty,
        string $sumber,
        ?string $keterangan = null,
        ?int $transaksiId = null,
    ): self {
        return DB::transaction(function () use ($produk, $jenis, $qty, $sumber, $keterangan, $transaksiId) {
            $produk = Produk::lockForUpdate()->findOrFail($produk->id);

            $sebelum = $produk->stok;
            $sesudah = $jenis === 'masuk' ? $sebelum + $qty : $sebelum - $qty;

            if ($sesudah < 0) {
                abort(422, "Stok {$produk->nama_produk} tidak mencukupi (sisa {$sebelum}).");
            }

            $produk->update(['stok' => $sesudah]);

            return self::create([
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama_produk,
                'jenis' => $jenis,
                'sumber' => $sumber,
                'qty' => $qty,
                'stok_sebelum' => $sebelum,
                'stok_sesudah' => $sesudah,
                'keterangan' => $keterangan,
                'transaksi_id' => $transaksiId,
                'user_id' => Auth::id(),
            ]);
        });
    }
}

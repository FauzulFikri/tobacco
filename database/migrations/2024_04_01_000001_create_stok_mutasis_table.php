<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat barang masuk & barang keluar (kartu stok).
     * Setiap perubahan stok dicatat di sini: stok awal, pembelian/restok,
     * penjualan, barang rusak/hilang, dan penyesuaian stok opname.
     */
    public function up(): void
    {
        Schema::create('stok_mutasis', function (Blueprint $table) {
            $table->id();
            // nullOnDelete: riwayat tetap ada walau produk/user/transaksi dihapus
            $table->foreignId('produk_id')->nullable()->constrained('produks')->nullOnDelete();
            $table->string('nama_produk'); // snapshot nama produk
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->string('sumber', 30); // stok_awal, pembelian, retur_pelanggan, penjualan, rusak, hilang, retur_supplier, penyesuaian
            $table->integer('qty');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->string('keterangan')->nullable();
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksis')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['jenis', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_mutasis');
    }
};

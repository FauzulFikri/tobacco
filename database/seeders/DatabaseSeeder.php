<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\StokMutasi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun default 3 role sesuai use case diagram di proposal
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@istanatembakau.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kasir 1',
            'username' => 'kasir1',
            'email' => 'kasir1@istanatembakau.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);

        User::create([
            'name' => 'Pimpinan',
            'username' => 'pimpinan',
            'email' => 'pimpinan@istanatembakau.test',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
        ]);

        // Kategori sesuai proposal
        $rokok = Kategori::create(['nama_kategori' => 'Rokok']);
        $tembakau = Kategori::create(['nama_kategori' => 'Tembakau']);
        $aksesoris = Kategori::create(['nama_kategori' => 'Aksesoris']);
        Kategori::create(['nama_kategori' => 'Lainnya']);

        // Produk contoh sesuai mockup
        Produk::insert([
            ['kode_produk' => 'PRD001', 'nama_produk' => 'Djarum Super', 'kategori_id' => $rokok->id, 'harga' => 25000, 'stok' => 120, 'stok_minimum' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['kode_produk' => 'PRD002', 'nama_produk' => 'Gudang Garam', 'kategori_id' => $rokok->id, 'harga' => 27000, 'stok' => 95, 'stok_minimum' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['kode_produk' => 'PRD003', 'nama_produk' => 'Surya 16', 'kategori_id' => $rokok->id, 'harga' => 18000, 'stok' => 80, 'stok_minimum' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['kode_produk' => 'PRD004', 'nama_produk' => 'Sampoerna Mild', 'kategori_id' => $rokok->id, 'harga' => 24000, 'stok' => 60, 'stok_minimum' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['kode_produk' => 'PRD005', 'nama_produk' => 'Tembakau 50gr', 'kategori_id' => $tembakau->id, 'harga' => 15000, 'stok' => 150, 'stok_minimum' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['kode_produk' => 'PRD006', 'nama_produk' => 'Kertas Paper', 'kategori_id' => $aksesoris->id, 'harga' => 5000, 'stok' => 200, 'stok_minimum' => 20, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Catat stok awal produk contoh di kartu stok
        foreach (Produk::all() as $produk) {
            StokMutasi::create([
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama_produk,
                'jenis' => 'masuk',
                'sumber' => 'stok_awal',
                'qty' => $produk->stok,
                'stok_sebelum' => 0,
                'stok_sesudah' => $produk->stok,
                'keterangan' => 'Stok awal (data contoh)',
            ]);
        }
    }
}

<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

// Halaman login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // Bisa diakses semua role yang login
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kelola Akun: semua role yang login
    Route::get('/akun', [AccountController::class, 'edit'])->name('akun.edit');
    Route::put('/akun', [AccountController::class, 'update'])->name('akun.update');
    Route::put('/akun/password', [AccountController::class, 'updatePassword'])->name('akun.password');

    // Transaksi/Kasir: admin & kasir
    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi/riwayat', [TransaksiController::class, 'riwayat'])->name('transaksi.riwayat');
        Route::get('/transaksi/{transaksi}/nota', [TransaksiController::class, 'nota'])->name('transaksi.nota');
    });

    // Produk & Kategori: khusus admin (kasir tidak punya akses)
    Route::middleware('role:admin')->group(function () {
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');

        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

        // Stok barang: pencatatan barang masuk & keluar
        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::post('/stok', [StokController::class, 'store'])->name('stok.store');
    });

    // Laporan: admin & pimpinan
    Route::middleware('role:admin,pimpinan')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
        Route::get('/laporan/stok', [LaporanController::class, 'stok'])->name('laporan.stok');
        Route::get('/laporan/stok/export-pdf', [LaporanController::class, 'stokPdf'])->name('laporan.stok.pdf');
        Route::get('/laporan/produk-terjual', [LaporanController::class, 'produkTerjual'])->name('laporan.produk-terjual');
        Route::get('/laporan/produk-terjual/export-pdf', [LaporanController::class, 'produkTerjualPdf'])->name('laporan.produk-terjual.pdf');
    });

    // Pengguna: khusus admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
    });
});

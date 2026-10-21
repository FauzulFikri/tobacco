<?php

namespace App\Providers;

use App\Models\Produk;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tampilan pagination mengikuti Bootstrap 5 (dipakai di layout)
        Paginator::useBootstrapFive();

        // Data pengingat stok menipis untuk lonceng notifikasi & pop-up di layout
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            if (! $user) {
                return;
            }

            $stokMenipis = Produk::stokMenipis()
                ->orderBy('stok')
                ->orderBy('nama_produk')
                ->get(['id', 'kode_produk', 'nama_produk', 'stok', 'stok_minimum']);

            // Pop-up pengingat ditampilkan sekali per sesi login untuk admin & pimpinan
            $tampilPopupStok = false;
            if ($stokMenipis->isNotEmpty()
                && in_array($user->role, ['admin', 'pimpinan'])
                && ! session('pengingat_stok_ditampilkan')) {
                $tampilPopupStok = true;
                session()->put('pengingat_stok_ditampilkan', true);
            }

            $view->with([
                'notifStok' => $stokMenipis,
                'notifStokJson' => Produk::untukNotifikasi($stokMenipis),
                'tampilPopupStok' => $tampilPopupStok,
            ]);
        });
    }
}

<?php

declare(strict_types=1);

use App\Livewire\Kurir\Info;
use App\Livewire\Kurir\Login;
use App\Livewire\Kurir\Profil;
use App\Livewire\Kurir\Beranda;
use App\Livewire\Kurir\Pesanan;
use App\Livewire\Kurir\Pembayaran;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Kurir\DetailPesanan;
use Illuminate\Support\Facades\Route;
use App\Livewire\Kurir\DetailPembayaran;
use App\Livewire\Kurir\Components\OfflinePage;
use App\Livewire\Pelanggan\Beranda as BerandaPelanggan;
use App\Livewire\Pelanggan\Pesanan as PesananPelanggan;
use App\Livewire\Pelanggan\Profil as ProfilPelanggan;
use App\Livewire\Pelanggan\DetailPesanan as DetailPesananPelanggan;

// Landing Page
Route::get('/', function () {
    return view('landing-page');
})->name('index');

// Kurir Routes
Route::prefix('kurir')->name('kurir.')->group(function () {
    // Offline Page (Accessible tanpa auth)
    Route::get('/offline', OfflinePage::class)->name('offline');

    // Guest Routes (Belum Login)
    Route::middleware('guest:courier')->group(function () {
        Route::get('/login', Login::class)->name('login');
    });

    // Protected Routes (Harus Login)
    Route::middleware('auth:courier')->group(function () {
        Route::get('/', Beranda::class)->name('beranda');
        Route::get('/beranda', Beranda::class)->name('beranda.alt');
        Route::get('/pesanan', Pesanan::class)->name('pesanan');
        Route::get('/pesanan/{id}', DetailPesanan::class)->name('pesanan.detail');
        Route::get('/pembayaran', Pembayaran::class)->name('pembayaran');
        Route::get('/pembayaran/{id}', DetailPembayaran::class)->name('pembayaran.detail');
        Route::get('/info', Info::class)->name('info');
        Route::get('/profil', Profil::class)->name('profil');

        // Logout
        Route::post('/logout', function () {
            Auth::guard('courier')->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('kurir.login');
        })->name('logout');
    });
});

// Pelanggan Routes
Route::prefix('pelanggan')->name('pelanggan.')->group(function () {
    // Guest Routes (Belum Login) - untuk development, bisa diakses tanpa auth dulu
    // Route::middleware('guest:customer')->group(function () {
    //     Route::get('/login', LoginPelanggan::class)->name('login');
    // });

    // Protected Routes (Harus Login) - sementara tanpa auth untuk testing tampilan
    // Route::middleware('auth:customer')->group(function () {
        Route::get('/', BerandaPelanggan::class)->name('beranda');
        Route::get('/beranda', BerandaPelanggan::class)->name('beranda.alt');
        Route::get('/pesanan', PesananPelanggan::class)->name('pesanan');
        Route::get('/detail-pesanan', DetailPesananPelanggan::class)->name('pesanan.detail');
        Route::get('/profil', ProfilPelanggan::class)->name('profil');

        // Logout (uncomment setelah auth siap)
        // Route::post('/logout', function () {
        //     Auth::guard('customer')->logout();
        //     session()->invalidate();
        //     session()->regenerateToken();
        //     return redirect()->route('pelanggan.login');
        // })->name('logout');
    // });
});

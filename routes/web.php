<?php

declare(strict_types=1);

use App\Livewire\Kurir\Info;
use App\Livewire\Kurir\Login;
use App\Livewire\Kurir\Profil;
use App\Livewire\Kurir\Beranda;
use App\Livewire\Kurir\Pesanan;
use App\Livewire\Kurir\Pembayaran;
use App\Livewire\Kurir\DetailPesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', function () {
    return view('landing-page');
})->name('index');

// Kurir Routes
Route::prefix('kurir')->name('kurir.')->group(function () {
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

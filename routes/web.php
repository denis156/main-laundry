<?php

declare(strict_types=1);

use App\Livewire\Kurir\Info;
use App\Livewire\Kurir\Profil;
use App\Livewire\Kurir\Beranda;
use App\Livewire\Kurir\Pesanan;
use App\Livewire\Kurir\Pembayaran;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing-page');
})->name('index');

Route::prefix('kurir')->name('kurir.')->group(function () {
    Route::get('/', Beranda::class)->name('beranda');
    Route::get('/pesanan', Pesanan::class)->name('pesanan');
    Route::get('/pembayaran', Pembayaran::class)->name('pembayaran');
    Route::get('/info', Info::class)->name('info');
    Route::get('/profil', Profil::class)->name('profil');
});

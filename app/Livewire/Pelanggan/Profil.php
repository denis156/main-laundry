<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Profil Saya')]
#[Layout('components.layouts.pelanggan')]
class Profil extends Component
{
    use Toast;

    public function mount(): void
    {
        // Tampilkan toast dari session jika ada
        if (session()->has('warning')) {
            $this->warning(
                title: 'Profil Belum Lengkap!',
                description: session('warning'),
                position: 'toast-top toast-end',
                timeout: 5000
            );
        }

        if (session()->has('success')) {
            $this->success(
                title: 'Login Berhasil!',
                description: session('success'),
                position: 'toast-top toast-end',
                timeout: 3000
            );
        }
    }

    public function render()
    {
        return view('livewire.pelanggan.profil');
    }
}

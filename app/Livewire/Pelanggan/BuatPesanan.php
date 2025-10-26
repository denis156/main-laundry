<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Buat Pesanan')]
#[Layout('components.layouts.pelanggan')]
class BuatPesanan extends Component
{
    public function render()
    {
        return view('livewire.pelanggan.buat-pesanan');
    }
}

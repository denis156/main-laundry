<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Pesanan Saya')]
#[Layout('components.layouts.pelanggan')]
class Pesanan extends Component
{
    public function render()
    {
        return view('livewire.pelanggan.pesanan');
    }
}

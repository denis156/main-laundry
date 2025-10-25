<?php

namespace App\Livewire\Pelanggan;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Beranda Pelanggan')]
#[Layout('components.layouts.pelanggan')]
class Beranda extends Component
{
    public function render()
    {
        return view('livewire.pelanggan.beranda');
    }
}

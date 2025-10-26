<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Profil Saya')]
#[Layout('components.layouts.pelanggan')]
class Profil extends Component
{
    public function render()
    {
        return view('livewire.pelanggan.profil');
    }
}

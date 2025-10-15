<?php

namespace App\Livewire\Kurir;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.mobile')]
class Pesanan extends Component
{
    public function render()
    {
        return view('livewire.kurir.pesanan');
    }
}

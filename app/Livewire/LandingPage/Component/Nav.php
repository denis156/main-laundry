<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage\Component;

use Livewire\Component;

class Nav extends Component
{
    public $activeSection = 'beranda';

    public $menuItems = [
        'beranda' => 'Beranda',
        'untuk-mu' => 'Untuk Mu',
        'layanan' => 'Layanan',
        'kontak' => 'Kontak'
    ];

    public function render()
    {
        return view('livewire.landing-page.component.nav');
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage\Component;

use Livewire\Component;

class Nav extends Component
{
    public $activeSection = 'beranda';

    public $menuItems = [
        'beranda' => 'Beranda',
        'layanan' => 'Layanan',
        'tentang-kami' => 'Tentang Kami',
        'cara-kerja' => 'Cara Kerja',
        'kontak' => 'Kontak'
    ];

    public function render()
    {
        return view('livewire.landing-page.component.nav');
    }
}

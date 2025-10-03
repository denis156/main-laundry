<?php

namespace App\Livewire\LandingPage\Component;

use Livewire\Component;

class Navbar extends Component
{
    public $activeSection = 'beranda';
    
    public $menuItems = [
        'beranda' => 'Beranda',
        'layanan' => 'Layanan', 
        'harga' => 'Harga',
        'promo' => 'Promo',
        'faq' => 'FAQ',
        'tentang' => 'Tentang',
        'testimoni' => 'Testimoni',
        'lokasi' => 'Lokasi',
        'reservasi' => 'Reservasi'
    ];

    public function render()
    {
        return view('livewire.landing-page.component.navbar');
    }
}

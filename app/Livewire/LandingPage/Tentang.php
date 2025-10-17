<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use Livewire\Component;

class Tentang extends Component
{
    public $values = [
        [
            'graphic' => 'ibu&anak-nyuci.svg',
            'title' => 'Solusi Hidup Praktis',
            'description' => 'Kami hadir untuk membuat hidup lebih simpel. Main Laundry adalah langkah awal dari ekosistem Main Group yang menghadirkan kemudahan dalam setiap aspek kehidupan.',
            'color' => 'primary'
        ],
        [
            'graphic' => 'wanita-menjemur.svg',
            'title' => 'Kualitas Tanpa Kompromi',
            'description' => 'Setiap layanan dirancang dengan standar tinggi. Dari cucian bersih hingga pengantaran tepat waktu, kepuasan Anda adalah prioritas kami.',
            'color' => 'accent'
        ],
        [
            'graphic' => 'pria-menjemur.svg',
            'title' => 'Inovasi Berkelanjutan',
            'description' => 'Main Laundry adalah pintu gerbang menuju ekosistem Main Group yang terus berkembang, menghadirkan solusi inovatif untuk masyarakat Indonesia.',
            'color' => 'secondary'
        ]
    ];

    public function render()
    {
        return view('livewire.landing-page.tentang');
    }
}

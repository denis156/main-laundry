<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use Livewire\Component;

class Kontak extends Component
{
    public $whatsappNumber = '6281234567890';
    public $whatsappMessage = 'Halo Main Laundry, saya mau tanya-tanya dulu';

    public $contacts = [
        [
            'icon' => 'mdi.phone',
            'title' => 'Telepon',
            'value' => '+62 812-3456-7890',
            'link' => 'tel:+6281234567890',
            'color' => 'primary'
        ],
        [
            'icon' => 'mdi.whatsapp',
            'title' => 'WhatsApp',
            'value' => '+62 812-3456-7890',
            'link' => 'https://wa.me/6281234567890',
            'color' => 'success'
        ],
        [
            'icon' => 'mdi.email',
            'title' => 'Email',
            'value' => 'info@mainlaundry.com',
            'link' => 'mailto:info@mainlaundry.com',
            'color' => 'accent'
        ],
        [
            'icon' => 'mdi.instagram',
            'title' => 'Instagram',
            'value' => '@main.laundry',
            'link' => 'https://instagram.com/main.laundry',
            'color' => 'secondary'
        ]
    ];

    public $address = [
        'name' => 'Main Group Cabang Kendari',
        'street' => '2G58+5X6, Lalolara',
        'city' => 'Kec. Kambu, Kota Kendari, Sulawesi Tenggara',
        'mapEmbed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3980.1376789796395!2d122.51482267562864!3d-3.9920960959816587!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d988d00245398a3%3A0x2d68c413fab2ecef!2sMain%20Group%20Cabang%20Kendari!5e0!3m2!1sid!2sid!4v1760693476673!5m2!1sid!2sid'
    ];

    public function render()
    {
        return view('livewire.landing-page.kontak');
    }
}

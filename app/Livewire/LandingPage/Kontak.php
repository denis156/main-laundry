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
        'name' => 'Kota Kendari',
        'street' => '',
        'city' => '',
        'mapEmbed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d142954.49995489785!2d122.53704485000002!3d-3.9850497!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d98ecde0b6b7183%3A0x1397347f9e562fc7!2sKendari%2C%20Kota%20Kendari%2C%20Sulawesi%20Tenggara!5e1!3m2!1sid!2sid!4v1760711897861!5m2!1sid!2sid'

    ];

    public function render()
    {
        return view('livewire.landing-page.kontak');
    }
}

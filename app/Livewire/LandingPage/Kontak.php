<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use Livewire\Component;

class Kontak extends Component
{
    public $whatsappNumber;
    public $whatsappMessage = 'Halo Main Laundry, saya mau tanya-tanya dulu';

    public $contacts = [];

    public $address = [
        'name' => 'Kota Kendari',
        'street' => '',
        'city' => '',
        'mapEmbed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d142954.49995489785!2d122.53704485000002!3d-3.9850497!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d98ecde0b6b7183%3A0x1397347f9e562fc7!2sKendari%2C%20Kota%20Kendari%2C%20Sulawesi%20Tenggara!5e1!3m2!1sid!2sid!4v1760711897861!5m2!1sid!2sid'

    ];

    public function mount()
    {
        $phone = config('sosmed.phone');
        $email = config('sosmed.email');
        $instagram = config('sosmed.instagram');

        // Set WhatsApp number (remove +, spaces, dashes)
        $this->whatsappNumber = str_replace(['+', ' ', '-'], '', $phone);

        // Build contacts array dynamically
        $this->contacts = [
            [
                'icon' => 'mdi.phone',
                'title' => 'Telepon',
                'value' => $phone,
                'link' => 'tel:' . str_replace([' ', '-'], '', $phone),
                'color' => 'primary'
            ],
            [
                'icon' => 'mdi.whatsapp',
                'title' => 'WhatsApp',
                'value' => $phone,
                'link' => 'https://wa.me/' . str_replace(['+', ' ', '-'], '', $phone),
                'color' => 'success'
            ],
            [
                'icon' => 'mdi.email',
                'title' => 'Email',
                'value' => $email,
                'link' => 'mailto:' . $email,
                'color' => 'accent'
            ],
        ];

        // Add Instagram only if configured
        if ($instagram) {
            $this->contacts[] = [
                'icon' => 'mdi.instagram',
                'title' => 'Instagram',
                'value' => '@' . str_replace(['https://instagram.com/', 'https://www.instagram.com/', '@'], '', $instagram),
                'link' => $instagram,
                'color' => 'secondary'
            ];
        }
    }

    public function render()
    {
        return view('livewire.landing-page.kontak');
    }
}

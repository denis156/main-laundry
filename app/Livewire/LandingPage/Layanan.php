<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use App\Models\Service;
use Livewire\Component;

class Layanan extends Component
{
    public $features = [
        [
            'graphic' => 'kurir.svg',
            'title' => 'Jemput & Antar GRATIS',
            'description' => 'Ke seluruh Kota Kendari! Tinggal pesan, kurir kami langsung meluncur!',
            'color' => 'accent'
        ],
        [
            'graphic' => 'kaos-kotor-menjadi-kaos-bersinar.svg',
            'title' => 'Harga Termurah SeIndonesia',
            'description' => 'Mulai Rp 3.000/kg! Ga ada lagi alasan untuk males nyuci.',
            'color' => 'accent'
        ],
        [
            'graphic' => 'kaos-putih-bersinar.svg',
            'title' => 'Kualitas Terjamin',
            'description' => 'Cucian bersih, wangi tahan lama, dan rapi. Garansi 100%!',
            'color' => 'accent'
        ],
        [
            'graphic' => 'smartphone.svg',
            'title' => 'Order Mudah',
            'description' => 'Cukup isi form pesanan, kami langsung jemput bajumu!',
            'color' => 'accent'
        ]
    ];

    public function render()
    {
        $services = Service::where('is_active', true)
            ->orderBy('price_per_kg', 'asc')
            ->get();

        // Get all SVG graphics from public/grafis folder
        $grafisPath = public_path('grafis');
        $graphics = [];

        if (is_dir($grafisPath)) {
            $files = scandir($grafisPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
                    $graphics[] = $file;
                }
            }
            // Shuffle untuk random order
            shuffle($graphics);
        }

        return view('livewire.landing-page.layanan', [
            'services' => $services,
            'graphics' => $graphics
        ]);
    }
}

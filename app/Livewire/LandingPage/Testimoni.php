<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use Livewire\Component;

class Testimoni extends Component
{
    public $testimonials = [];
    public $ratings = [];

    public function mount()
    {
        $this->testimonials = [
            [
                'name' => 'Sarah M.',
                'initials' => 'SM',
                'rating' => 4,
                'text' => 'Pelayanan sangat memuaskan! Pakaian bersih, wangi, dan rapi. Proses cepat dan harga terjangkau. Highly recommended!',
            ],
            [
                'name' => 'Budi P.',
                'initials' => 'BP',
                'rating' => 5,
                'text' => 'Layanan express 6 jam benar-benar membantu saat saya butuh pakaian mendadak. Kualitas cucian premium dan staff sangat ramah!',
            ],
            [
                'name' => 'Dewi F.',
                'initials' => 'DF',
                'rating' => 4,
                'text' => 'Sudah langganan hampir 2 tahun. Konsisten dengan kualitas dan pelayanannya. Layanan antar jemput gratis sangat membantu!',
            ],
            [
                'name' => 'Ahmad R.',
                'initials' => 'AR',
                'rating' => 3,
                'text' => 'Dry cleaning untuk suit saya hasilnya sangat memuaskan. Bahan tetap bagus dan tidak rusak. Professional service!',
            ],
            [
                'name' => 'Linda N.',
                'initials' => 'LN',
                'rating' => 4,
                'text' => 'Cuci sepatu disini hasilnya seperti baru lagi! Detail pembersihan sangat teliti. Harga juga kompetitif. Puas terus!',
            ],
            [
                'name' => 'Rudi H.',
                'initials' => 'RH',
                'rating' => 5,
                'text' => 'Membership worth it banget! Diskon dan benefit yang ditawarkan sangat membantu. Customer service responsif dan helpful!',
            ],
        ];

        // Inisialisasi array ratings untuk wire:model
        foreach ($this->testimonials as $index => $testimonial) {
            $this->ratings[$index] = $testimonial['rating'];
        }
    }

    public function render()
    {
        return view('livewire.landing-page.testimoni');
    }
}

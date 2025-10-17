<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use Livewire\Component;

class CaraKerja extends Component
{
    public $steps = [
        [
            'phase' => 'LANGKAH 1',
            'title' => 'Pesan & Pickup',
            'description' => 'Isi form online dengan mudah, kurir motor kami langsung konfirmasi via WhatsApp dan datang ke lokasi Anda.',
            'details' => [
                'Isi form pesanan online (nama, WA, alamat, layanan)',
                'Kurir konfirmasi & request lokasi',
                'Kurir datang ke rumah Anda',
                'Timbang pakaian di depan Anda',
                'Bisa bayar saat pickup atau pengantaran'
            ],
            'icon' => 'mdi.cellphone-check',
            'graphic' => 'smartphone.svg',
            'color' => 'accent'
        ],
        [
            'phase' => 'LANGKAH 2',
            'title' => 'Transit ke Pos',
            'description' => 'Pakaian Anda dibawa ke Pos Loading terdekat untuk distribusi yang efisien ke tempat pencucian.',
            'details' => [
                'Pakaian dibawa ke Pos terdekat',
                'Disimpan sementara di Pos Loading',
                'Admin menerima notifikasi otomatis'
            ],
            'icon' => 'mdi.home-city',
            'graphic' => 'pos.svg',
            'color' => 'accent'
        ],
        [
            'phase' => 'LANGKAH 3',
            'title' => 'Transportasi ke Pencucian',
            'description' => 'Kurir mobil berkeliling mengambil cucian dari berbagai Pos dan membawanya ke tempat pencucian profesional.',
            'details' => [
                'Kurir mobil berkeliling ambil dari Pos',
                'Bawa ke tempat pencucian',
                'Efisien dan terorganisir'
            ],
            'icon' => 'mdi.truck-delivery',
            'graphic' => 'mobil.svg',
            'color' => 'accent'
        ],
        [
            'phase' => 'LANGKAH 4',
            'title' => 'Proses Pencucian',
            'description' => 'Pakaian Anda dicuci dengan mesin profesional dan detergen berkualitas untuk hasil maksimal.',
            'details' => [
                'Proses pencucian profesional',
                'Bersih, wangi, dan rapi',
                'Quality control ketat',
                'Siap untuk dikembalikan'
            ],
            'icon' => 'mdi.washing-machine',
            'graphic' => 'mesin-cuci.svg',
            'color' => 'accent'
        ],
        [
            'phase' => 'LANGKAH 5',
            'title' => 'Kembali ke Pos',
            'description' => 'Pakaian bersih Anda dikembalikan ke Pos asal, siap untuk diantar kembali ke rumah Anda.',
            'details' => [
                'Kurir mobil antar pakaian bersih ke Pos asal',
                'Kurir motor menerima notifikasi',
                'Siap untuk pengantaran'
            ],
            'icon' => 'mdi.package-variant-closed-check',
            'graphic' => 'pos.svg',
            'color' => 'accent'
        ],
        [
            'phase' => 'LANGKAH 6',
            'title' => 'Pengantaran ke Rumah',
            'description' => 'Kurir motor mengambil pakaian bersih dari Pos dan mengantarkan langsung ke rumah Anda. Selesai!',
            'details' => [
                'Kurir motor ambil dari Pos',
                'Antar ke rumah Anda',
                'Jika belum bayar, bayar saat pengantaran',
                'Pakaian bersih diterima!'
            ],
            'icon' => 'mdi.home-heart',
            'graphic' => 'kurir.svg',
            'color' => 'accent'
        ]
    ];

    public function render()
    {
        return view('livewire.landing-page.cara-kerja');
    }
}

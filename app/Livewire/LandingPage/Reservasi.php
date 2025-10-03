<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;

class Reservasi extends Component
{
    // Form Fields
    public $nama_lengkap = '';
    public $nomor_telepon = '';
    public $email = '';
    public $alamat = '';
    public $jenis_layanan = '';
    public $kecepatan = '';
    public $berat = '';
    public $tanggal_pickup = '';
    public $pewangi = '';
    public $detergen_sendiri = false;
    public $pisahkan_warna = false;
    public $pelembut = false;
    public $catatan = '';

    // Select Options
    public $layananOptions = [];
    public $kecepatanOptions = [];
    public $pewangiOptions = [];

    public function mount()
    {
        $this->layananOptions = [
            ['id' => 'cuci-kering', 'name' => 'Cuci + Kering'],
            ['id' => 'cuci-setrika', 'name' => 'Cuci + Setrika'],
            ['id' => 'setrika', 'name' => 'Setrika Saja'],
            ['id' => 'dry-cleaning', 'name' => 'Dry Cleaning'],
            ['id' => 'cuci-sepatu', 'name' => 'Cuci Sepatu'],
        ];

        $this->kecepatanOptions = [
            ['id' => 'reguler', 'name' => 'Reguler (2-3 hari)'],
            ['id' => 'express', 'name' => 'Express (24 jam)'],
            ['id' => 'kilat', 'name' => 'Kilat (6 jam)'],
        ];

        $this->pewangiOptions = [
            ['id' => 'lavender', 'name' => 'Lavender'],
            ['id' => 'rose', 'name' => 'Rose'],
            ['id' => 'fresh', 'name' => 'Fresh'],
            ['id' => 'sport', 'name' => 'Sport'],
            ['id' => 'baby', 'name' => 'Baby (hypoallergenic)'],
        ];
    }

    public function submit()
    {
        $this->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'email' => 'nullable|email',
            'alamat' => 'required|string',
            'jenis_layanan' => 'required',
            'kecepatan' => 'required',
            'berat' => 'required|numeric|min:1',
            'tanggal_pickup' => 'required',
        ]);

        // TODO: Proses submit reservasi (simpan ke database, kirim notifikasi, dll)

        // Tampilkan success message
        session()->flash('success', 'Reservasi berhasil dikirim! Tim kami akan segera menghubungi Anda.');

        // Reset form
        $this->reset();
    }

    public function render()
    {
        return view('livewire.landing-page.reservasi');
    }
}

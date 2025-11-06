<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan\Components;

use Livewire\Component;
use Livewire\Attributes\On;
use Mary\Traits\Toast;

/**
 * Modal untuk menampilkan peringatan lengkapi profil
 *
 * Component ini akan menampilkan modal jika profil pelanggan belum lengkap
 * dan memberikan opsi untuk langsung menuju halaman profil
 */
class LengkapiProfilModal extends Component
{
    use Toast;

    public bool $showModal = false;
    public string $customerName = '';
    public string $redirectFrom = '';

    /**
     * Listen untuk event showLengkapiProfilModal
     */
    #[On('showLengkapiProfilModal')]
    public function show($customerName = '', $redirectFrom = '')
    {
        $this->customerName = $customerName;
        $this->redirectFrom = $redirectFrom;
        $this->showModal = true;
    }

    /**
     * Listen untuk event hideLengkapiProfilModal
     */
    #[On('hideLengkapiProfilModal')]
    public function hide()
    {
        $this->showModal = false;
    }

    /**
     * Redirect ke halaman profil
     */
    public function goToProfil()
    {
        $this->showModal = false;

        $this->info(
            title: 'Lengkapi Profil',
            description: 'Silakan lengkapi data profil Anda terlebih dahulu.',
            position: 'toast-top toast-end',
            timeout: 3000,
            redirectTo: route('pelanggan.profil')
        );
    }

    /**
     * Close modal dan kembali ke halaman sebelumnya
     */
    public function close()
    {
        $this->showModal = false;

        // Jika dari buat pesanan, redirect ke beranda
        if ($this->redirectFrom === 'buat-pesanan') {
            $this->redirect(route('pelanggan.beranda'), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.pelanggan.components.lengkapi-profil-modal');
    }
}
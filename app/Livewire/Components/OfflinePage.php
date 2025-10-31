<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Contracts\View\View;

#[Title('Offline')]
class OfflinePage extends Component
{
    public string $guardType = 'kurir'; // Default guard

    public function mount(?string $guard = null): void
    {
        // Detect guard dari parameter atau dari auth
        if ($guard) {
            $this->guardType = $guard;
        } elseif (auth()->guard('customer')->check()) {
            $this->guardType = 'pelanggan';
        } elseif (auth()->guard('courier')->check()) {
            $this->guardType = 'kurir';
        }
    }

    /**
     * Get beranda route berdasarkan guard type
     */
    public function getBerandaRoute(): string
    {
        return $this->guardType === 'pelanggan'
            ? route('pelanggan.beranda')
            : route('kurir.beranda');
    }

    /**
     * Reload halaman untuk coba koneksi lagi
     */
    public function retry(): void
    {
        // Redirect ke beranda untuk force refresh
        $this->redirect($this->getBerandaRoute(), navigate: false);
    }

    public function render(): View
    {
        // Tentukan layout berdasarkan guard type
        $layout = $this->guardType === 'pelanggan'
            ? 'components.layouts.pelanggan'
            : 'components.layouts.kurir';

        // Return view dengan layout menggunakan extends
        return view('livewire.components.offline-page', [
            'layout' => $layout
        ]);
    }
}

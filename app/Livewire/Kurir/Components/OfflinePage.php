<?php

declare(strict_types=1);

namespace App\Livewire\Kurir\Components;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Offline')]
#[Layout('components.layouts.kurir')]
class OfflinePage extends Component
{
    /**
     * Reload halaman untuk coba koneksi lagi
     */
    public function retry(): void
    {
        // Redirect ke beranda untuk force refresh
        $this->redirect(route('kurir.beranda'), navigate: false);
    }

    public function render()
    {
        return view('livewire.kurir.components.offline-page');
    }
}

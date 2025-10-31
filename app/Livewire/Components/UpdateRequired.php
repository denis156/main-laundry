<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;

class UpdateRequired extends Component
{
    public bool $showModal = false;

    // Legacy properties untuk backward compatibility
    // Tidak digunakan tapi tetap ada untuk prevent hydration error
    public string $currentVersion = '';
    public string $newVersion = '';

    /**
     * Listen untuk event dari JavaScript saat SW update tersedia
     */
    #[On('sw-update-available')]
    public function showUpdateModal(): void
    {
        $this->showModal = true;
    }

    /**
     * Reload page untuk apply update
     */
    public function reloadApp(): void
    {
        // Tutup modal dulu sebelum reload
        $this->showModal = false;

        // Dispatch reload setelah modal tertutup
        $this->dispatch('force-reload-app');
    }

    public function render()
    {
        return view('livewire.components.update-required');
    }
}

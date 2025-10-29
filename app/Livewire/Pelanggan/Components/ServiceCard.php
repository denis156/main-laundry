<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan\Components;

use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\Computed;

class ServiceCard extends Component
{
    /**
     * Get active services
     */
    #[Computed]
    public function services()
    {
        return Service::where('is_active', true)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Format price to Rupiah
     */
    public function formatPrice(float $price): string
    {
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    /**
     * Format duration text
     */
    public function formatDuration(int $days): string
    {
        return $days . ' Hari Masa Kerja';
    }

    public function render()
    {
        return view('livewire.pelanggan.components.service-card');
    }
}

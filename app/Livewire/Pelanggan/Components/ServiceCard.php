<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan\Components;

use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\Computed;

class ServiceCard extends Component
{
    /**
     * Get active services (limit 4 for display)
     */
    #[Computed]
    public function services()
    {
        return Service::where('is_active', true)
            ->orderBy('created_at', 'asc')
            ->limit(4)
            ->get();
    }

    /**
     * Get border class based on index
     */
    public function getBorderClass(int $index): string
    {
        $borderClasses = [
            0 => 'border-b-6 border-r-6',
            1 => 'border-b-6 border-l-6',
            2 => 'border-t-6 border-r-6',
            3 => 'border-t-6 border-l-6',
        ];

        return $borderClasses[$index] ?? 'border-b-6 border-r-6';
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

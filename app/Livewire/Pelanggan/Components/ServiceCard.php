<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan\Components;

use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Helper\Database\ServiceHelper;

class ServiceCard extends Component
{
    /**
     * Get active services with proper ordering
     * Featured services first, then by sort_order
     */
    #[Computed]
    public function services()
    {
        return Service::where('is_active', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    /**
     * Check if service is featured
     */
    public function isFeatured(Service $service): bool
    {
        return $service->is_featured ?? false;
    }

    /**
     * Get badge settings for featured service
     */
    public function getBadgeSettings(Service $service): ?array
    {
        return ServiceHelper::getBadgeSettings($service);
    }

    /**
     * Get features list
     */
    public function getFeatures(Service $service): array
    {
        return ServiceHelper::getFeatures($service);
    }

    public function render()
    {
        return view('livewire.pelanggan.components.service-card');
    }
}

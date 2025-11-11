<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Helper\Database\ServiceHelper;

#[Title('Detail Layanan')]
#[Layout('components.layouts.pelanggan')]
class DetailLayanan extends Component
{
    public Service $service;

    /**
     * Mount component with service ID
     */
    public function mount(int $id): void
    {
        $this->service = Service::where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * Get features list
     */
    public function getFeatures(): array
    {
        return ServiceHelper::getFeatures($this->service);
    }

    /**
     * Get includes list
     */
    public function getIncludes(): array
    {
        return $this->service->data['includes'] ?? [];
    }

    /**
     * Get restrictions list
     */
    public function getRestrictions(): array
    {
        return $this->service->data['restrictions'] ?? [];
    }

    /**
     * Get materials used list
     */
    public function getMaterials(): array
    {
        return $this->service->data['materials_used'] ?? [];
    }

    /**
     * Get pricing tiers (if per_kg)
     */
    public function getPricingTiers(): array
    {
        return $this->service->data['pricing_tiers'] ?? [];
    }

    /**
     * Check if service is featured
     */
    public function isFeatured(): bool
    {
        return $this->service->is_featured ?? false;
    }

    /**
     * Get badge settings
     */
    public function getBadgeSettings(): ?array
    {
        return ServiceHelper::getBadgeSettings($this->service);
    }

    public function render()
    {
        return view('livewire.pelanggan.detail-layanan');
    }
}

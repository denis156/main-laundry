<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan\Components;

use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Mary\Traits\Toast;

class ServiceCard extends Component
{
    use Toast;

    /**
     * Apakah component ini berada di halaman buat pesanan?
     * Jika true, akan emit event. Jika false, akan redirect dengan toast.
     */
    public bool $isOnOrderPage = false;

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
     * Handle service selection
     * - Jika di halaman buat pesanan: emit event ke parent
     * - Jika di halaman lain: redirect ke buat pesanan dengan session
     */
    public function selectService(int $serviceId): void
    {
        $service = Service::find($serviceId);

        if (!$service || !$service->is_active) {
            $this->error('Layanan tidak tersedia');
            return;
        }

        if ($this->isOnOrderPage) {
            // Jika sudah di halaman buat pesanan, emit event ke parent component
            $this->dispatch('service-selected', serviceId: $serviceId);
        } else {
            // Jika di halaman lain, simpan ke session dan redirect dengan toast
            session()->flash('selected_service_id', $serviceId);
            session()->flash('selected_service_name', $service->name);

            $this->success(
                title: 'Layanan dipilih!',
                description: "Anda memilih layanan: {$service->name}",
                position: 'toast-top toast-end',
                timeout: 3000,
                redirectTo: route('pelanggan.buat-pesanan')
            );
        }
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

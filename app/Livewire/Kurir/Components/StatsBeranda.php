<?php

declare(strict_types=1);

namespace App\Livewire\Kurir\Components;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class StatsBeranda extends Component
{
    /**
     * Refresh stats - dipanggil dari JavaScript saat menerima broadcast event
     */
    #[On('refresh-stats')]
    public function refreshStats(): void
    {
        // Refresh computed properties untuk load data terbaru
        unset($this->confirmedTransactions);
        unset($this->completedTransactions);
    }

    #[Computed]
    public function courier()
    {
        return Auth::guard('courier')->user();
    }

    #[Computed]
    public function confirmedTransactions()
    {
        return $this->courier?->transactions()
            ->where('workflow_status', 'confirmed')
            ->count() ?? 0;
    }

    #[Computed]
    public function completedTransactions()
    {
        return $this->courier?->transactions()
            ->where('workflow_status', 'delivered')
            ->count() ?? 0;
    }

    public function render()
    {
        return view('livewire.kurir.components.stats-beranda');
    }
}

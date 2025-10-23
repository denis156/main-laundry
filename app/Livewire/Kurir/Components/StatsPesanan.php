<?php

declare(strict_types=1);

namespace App\Livewire\Kurir\Components;

use App\Helper\TransactionAreaFilter;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class StatsPesanan extends Component
{
    /**
     * Refresh stats - dipanggil dari JavaScript saat menerima broadcast event
     */
    #[On('refresh-stats')]
    public function refreshStats(): void
    {
        // Refresh computed properties untuk load data terbaru
        unset($this->stats);
    }

    /**
     * Get statistik pesanan (hanya dari area layanan pos kurir)
     * Hanya tampilkan Selesai dan Batal
     */
    #[Computed]
    public function stats(): array
    {
        $courier = Auth::guard('courier')->user();

        // Load pos dengan area layanan
        $assignedPos = $courier->assignedPos;

        // Base query dengan filter area menggunakan helper
        $baseQuery = function () use ($assignedPos) {
            $query = Transaction::query();
            TransactionAreaFilter::applyFilter($query, $assignedPos);
            return $query;
        };

        $deliveredCount = $baseQuery()
            ->where('courier_motorcycle_id', $courier->id)
            ->where('workflow_status', 'delivered')
            ->count();

        $cancelledCount = $baseQuery()
            ->where('courier_motorcycle_id', $courier->id)
            ->where('workflow_status', 'cancelled')
            ->count();

        return [
            'delivered_count' => $deliveredCount,
            'cancelled_count' => $cancelledCount,
        ];
    }

    public function render()
    {
        return view('livewire.kurir.components.stats-pesanan');
    }
}

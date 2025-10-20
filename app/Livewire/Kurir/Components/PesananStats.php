<?php

declare(strict_types=1);

namespace App\Livewire\Kurir\Components;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class PesananStats extends Component
{
    /**
     * Get statistik pesanan (hanya dari area layanan pos kurir)
     */
    #[Computed]
    public function stats(): array
    {
        $courier = Auth::guard('courier')->user();

        // Load pos dengan area layanan
        $assignedPos = $courier->assignedPos;

        // Base query dengan filter area
        $baseQuery = function () use ($assignedPos) {
            $query = Transaction::query();

            // Filter berdasarkan area layanan pos
            if ($assignedPos && !empty($assignedPos->area)) {
                $query->whereHas('customer', function ($q) use ($assignedPos) {
                    $q->where(function ($subQ) use ($assignedPos) {
                        foreach ($assignedPos->area as $kelurahan) {
                            $subQ->orWhere('village_name', $kelurahan);
                        }
                        $subQ->orWhereNull('village_name');
                    });
                });
            }

            return $query;
        };

        $pendingCount = $baseQuery()
            ->where(function ($q) use ($courier) {
                $q->where('courier_motorcycle_id', $courier->id)
                    ->orWhereNull('courier_motorcycle_id');
            })
            ->where('workflow_status', 'pending_confirmation')
            ->count();

        $activeCount = $baseQuery()
            ->where('courier_motorcycle_id', $courier->id)
            ->whereIn('workflow_status', ['confirmed', 'picked_up', 'at_loading_post', 'in_washing', 'washing_completed', 'out_for_delivery'])
            ->count();

        $deliveredCount = $baseQuery()
            ->where('courier_motorcycle_id', $courier->id)
            ->where('workflow_status', 'delivered')
            ->count();

        $cancelledCount = $baseQuery()
            ->where('courier_motorcycle_id', $courier->id)
            ->where('workflow_status', 'cancelled')
            ->count();

        return [
            'pending_count' => $pendingCount,
            'active_count' => $activeCount,
            'delivered_count' => $deliveredCount,
            'cancelled_count' => $cancelledCount,
        ];
    }

    public function render()
    {
        return view('livewire.kurir.components.pesanan-stats');
    }
}

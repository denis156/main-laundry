<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use App\Models\Transaction;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class OrderNotification extends Component
{
    public int $pendingCount = 0;

    /**
     * Get jumlah pesanan pending_confirmation yang ada di area kurir
     */
    public function getPendingOrderCount(): int
    {
        $courier = Auth::guard('courier')->user();

        if (!$courier) {
            return 0;
        }

        $assignedPos = $courier->assignedPos;

        $query = Transaction::where('workflow_status', 'pending_confirmation')
            ->where(function ($q) use ($courier) {
                // Transaksi yang sudah di-assign ke kurir ini ATAU belum ada kurirnya
                $q->where('courier_motorcycle_id', $courier->id)
                    ->orWhereNull('courier_motorcycle_id');
            });

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

        return $query->count();
    }

    /**
     * Mount - Dispatch initial count saat component load
     */
    public function mount(): void
    {
        $this->pendingCount = $this->getPendingOrderCount();

        // Dispatch initial count untuk check apakah ada unnotified orders
        $this->dispatch('order-count-updated', count: $this->pendingCount);
    }

    /**
     * Check untuk pesanan baru - dipanggil oleh wire:poll
     * Dispatch count ke browser, JavaScript yang handle comparison
     */
    public function checkNewOrders(): void
    {
        $currentCount = $this->getPendingOrderCount();
        $this->pendingCount = $currentCount;

        // Dispatch count ke JavaScript untuk comparison dan play ringtone
        $this->dispatch('order-count-updated', count: $currentCount);
    }

    public function render()
    {
        return view('livewire.kurir.order-notification');
    }
}

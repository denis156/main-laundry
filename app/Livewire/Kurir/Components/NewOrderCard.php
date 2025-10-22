<?php

declare(strict_types=1);

namespace App\Livewire\Kurir\Components;

use App\Helper\TransactionAreaFilter;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class NewOrderCard extends Component
{
    /**
     * Refresh new orders - dipanggil dari JavaScript saat menerima broadcast event
     */
    #[On('refresh-new-orders')]
    public function refreshNewOrders(): void
    {
        // Refresh computed properties untuk load data terbaru
        unset($this->pendingConfirmationTransactions);
    }

    #[Computed]
    public function pendingConfirmationTransactions(): Collection
    {
        $courier = Auth::guard('courier')->user();

        // Load pos dengan area layanan
        $assignedPos = $courier->assignedPos;

        $query = \App\Models\Transaction::with(['customer', 'service', 'pos'])
            ->where(function ($q) use ($courier) {
                // Transaksi yang sudah di-assign ke kurir ini
                $q->where('courier_motorcycle_id', $courier->id)
                    // ATAU transaksi yang belum ada kurirnya (bisa diambil)
                    ->orWhereNull('courier_motorcycle_id');
            })
            ->where('workflow_status', 'pending_confirmation')
            ->whereNotNull('customer_id')
            ->whereNotNull('service_id')
            ->whereHas('customer')
            ->whereHas('service');

        // Filter berdasarkan area layanan pos menggunakan helper
        TransactionAreaFilter::applyFilter($query, $assignedPos);

        return $query->orderBy('order_date', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.kurir.components.new-order-card');
    }
}

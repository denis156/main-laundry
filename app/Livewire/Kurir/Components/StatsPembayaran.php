<?php

declare(strict_types=1);

namespace App\Livewire\Kurir\Components;

use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class StatsPembayaran extends Component
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
     * Get statistik pembayaran
     * Semua payment yang di-handle oleh kurir yang sedang login
     * Berdasarkan payment_status dari transaction
     */
    #[Computed]
    public function stats(): array
    {
        $courier = Auth::guard('courier')->user();

        // Count payments dengan status unpaid
        $unpaidCount = Payment::where('courier_motorcycle_id', $courier->id)
            ->whereHas('transaction', function ($q) {
                $q->where('payment_status', 'unpaid')
                    ->whereNotNull('customer_id')
                    ->whereNotNull('service_id')
                    ->whereHas('customer')
                    ->whereHas('service');
            })
            ->count();

        // Count payments dengan status paid
        $paidCount = Payment::where('courier_motorcycle_id', $courier->id)
            ->whereHas('transaction', function ($q) {
                $q->where('payment_status', 'paid')
                    ->whereNotNull('customer_id')
                    ->whereNotNull('service_id')
                    ->whereHas('customer')
                    ->whereHas('service');
            })
            ->count();

        // Total amount yang belum dibayar
        $unpaidTotal = Payment::where('courier_motorcycle_id', $courier->id)
            ->whereHas('transaction', function ($q) {
                $q->where('payment_status', 'unpaid')
                    ->whereNotNull('customer_id')
                    ->whereNotNull('service_id')
                    ->whereHas('customer')
                    ->whereHas('service');
            })
            ->sum('amount');

        return [
            'unpaid_count' => $unpaidCount,
            'paid_count' => $paidCount,
            'unpaid_total' => $unpaidTotal,
        ];
    }

    public function render()
    {
        return view('livewire.kurir.components.stats-pembayaran');
    }
}

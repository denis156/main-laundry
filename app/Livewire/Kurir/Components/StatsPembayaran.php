<?php

declare(strict_types=1);

namespace App\Livewire\Kurir\Components;

use App\Models\Transaction;
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
     * Semua transaksi yang di-handle oleh kurir yang sedang login
     * Terlepas dari workflow_status
     */
    #[Computed]
    public function stats(): array
    {
        $courier = Auth::guard('courier')->user();

        $unpaidCount = Transaction::where('courier_motorcycle_id', $courier->id)
            ->where('payment_status', 'unpaid')
            ->count();

        $paidCount = Transaction::where('courier_motorcycle_id', $courier->id)
            ->where('payment_status', 'paid')
            ->count();

        $unpaidTotal = Transaction::where('courier_motorcycle_id', $courier->id)
            ->where('payment_status', 'unpaid')
            ->sum('total_price');

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

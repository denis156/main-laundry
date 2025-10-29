<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan\Components;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class ActiveOrder extends Component
{
    /**
     * Get active transactions for customer (all status except delivered and cancelled)
     */
    #[Computed]
    public function activeTransactions(): Collection
    {
        $customer = Auth::guard('customer')->user();

        return Transaction::with(['service', 'courierMotorcycle'])
            ->where('customer_id', $customer->id)
            ->whereNotIn('workflow_status', ['delivered', 'cancelled'])
            ->orderBy('order_date', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.pelanggan.components.active-order');
    }
}

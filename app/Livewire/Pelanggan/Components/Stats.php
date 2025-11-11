<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan\Components;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class Stats extends Component
{
    /**
     * Get authenticated customer
     */
    #[Computed]
    public function customer()
    {
        return Auth::guard('customer')->user();
    }

    /**
     * Get active orders count (excluding 'delivered' and 'cancelled')
     */
    #[Computed]
    public function activeOrdersCount()
    {
        return Transaction::where('customer_id', $this->customer->id)
            ->whereNotIn('workflow_status', ['delivered', 'cancelled'])
            ->count();
    }

    /**
     * Get completed orders count
     */
    #[Computed]
    public function completedOrdersCount()
    {
        return Transaction::where('customer_id', $this->customer->id)
            ->where('workflow_status', 'delivered')
            ->count();
    }

    public function render()
    {
        return view('livewire.pelanggan.components.stats');
    }
}

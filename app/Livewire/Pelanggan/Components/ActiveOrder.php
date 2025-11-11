<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan\Components;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class ActiveOrder extends Component
{
    // Pagination
    public int $perPage = 3;
    public int $currentPage = 1;

    /**
     * Get total count active transactions untuk pagination
     */
    #[Computed]
    public function totalActiveTransactions(): int
    {
        $customer = Auth::guard('customer')->user();

        return Transaction::where('customer_id', $customer->id)
            ->whereNotIn('workflow_status', ['delivered', 'cancelled'])
            ->count();
    }

    /**
     * Get active transactions for customer (all status except delivered and cancelled)
     */
    #[Computed]
    public function activeTransactions(): Collection
    {
        $customer = Auth::guard('customer')->user();

        return Transaction::with(['courier', 'location'])
            ->where('customer_id', $customer->id)
            ->whereNotIn('workflow_status', ['delivered', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->skip(($this->currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();
    }

    /**
     * Check if has more data
     */
    #[Computed]
    public function hasMore(): bool
    {
        return ($this->perPage * $this->currentPage) < $this->totalActiveTransactions;
    }

    /**
     * Check if can load less
     */
    #[Computed]
    public function canLoadLess(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Load more data
     */
    public function loadMore(): void
    {
        $this->currentPage++;
        unset($this->activeTransactions);
        unset($this->totalActiveTransactions);
    }

    /**
     * Load less data
     */
    public function loadLess(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            unset($this->activeTransactions);
            unset($this->totalActiveTransactions);
        }
    }

    /**
     * Refresh active orders when notified via Echo
     */
    #[On('refresh-active-orders')]
    public function refreshActiveOrders(): void
    {
        unset($this->activeTransactions);
        unset($this->totalActiveTransactions);
    }

    public function render()
    {
        return view('livewire.pelanggan.components.active-order');
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Payment;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use App\Helper\Database\PaymentHelper;

#[Title('Pembayaran')]
#[Layout('components.layouts.kurir')]
class Pembayaran extends Component
{
    use Toast, WithPagination;

    public string $filter = 'all'; // all, with_proof, without_proof
    public string $search = '';

    // Pagination manual
    public int $perPage = 5;
    public int $currentPage = 1;

    /**
     * Get total count payment untuk pagination
     */
    #[Computed]
    public function totalPayments(): int
    {
        $courier = Auth::guard('courier')->user();

        $query = Payment::with(['transaction.customer', 'transaction.location', 'courier'])
            ->where('courier_id', $courier->id)
            ->whereHas('transaction', function ($q) {
                $q->whereNotNull('customer_id')
                    ->whereHas('customer');
            });

        // Filter berdasarkan bukti pembayaran (di JSONB)
        if ($this->filter === 'with_proof') {
            $query->whereRaw("data->>'proof_url' IS NOT NULL")
                  ->whereRaw("data->>'proof_url' != ''");
        } elseif ($this->filter === 'without_proof') {
            $query->where(function ($q) {
                $q->whereRaw("data->>'proof_url' IS NULL")
                  ->orWhereRaw("data->>'proof_url' = ''");
            });
        }

        // Search by invoice atau customer name (di JSONB)
        if (!empty($this->search)) {
            $query->whereHas('transaction', function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($subQ) {
                        $subQ->whereRaw("data->>'name' ILIKE ?", ['%' . $this->search . '%']);
                    });
            });
        }

        return $query->count();
    }

    /**
     * Get semua payment yang di-handle oleh kurir yang sedang login
     * - Tampilkan payment records dengan pagination manual
     * - Filter berdasarkan ada tidaknya bukti pembayaran
     */
    #[Computed]
    public function payments(): Collection
    {
        $courier = Auth::guard('courier')->user();

        $query = Payment::with(['transaction.customer', 'transaction.location', 'courier'])
            ->where('courier_id', $courier->id)
            ->whereHas('transaction', function ($q) {
                $q->whereNotNull('customer_id')
                    ->whereHas('customer');
            });

        // Filter berdasarkan bukti pembayaran (di JSONB)
        if ($this->filter === 'with_proof') {
            $query->whereRaw("data->>'proof_url' IS NOT NULL")
                  ->whereRaw("data->>'proof_url' != ''");
        } elseif ($this->filter === 'without_proof') {
            $query->where(function ($q) {
                $q->whereRaw("data->>'proof_url' IS NULL")
                  ->orWhereRaw("data->>'proof_url' = ''");
            });
        }

        // Search by invoice atau customer name (di JSONB)
        if (!empty($this->search)) {
            $query->whereHas('transaction', function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($subQ) {
                        $subQ->whereRaw("data->>'name' ILIKE ?", ['%' . $this->search . '%']);
                    });
            });
        }

        // Hitung limit berdasarkan currentPage
        $limit = $this->perPage * $this->currentPage;

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Tampilkan lebih banyak data
     */
    public function loadMore(): void
    {
        $this->currentPage++;
        unset($this->payments);
        unset($this->totalPayments);
    }

    /**
     * Tampilkan lebih sedikit data
     */
    public function loadLess(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            unset($this->payments);
            unset($this->totalPayments);
        }
    }

    /**
     * Check apakah masih ada data lagi
     */
    public function hasMore(): bool
    {
        return ($this->perPage * $this->currentPage) < $this->totalPayments;
    }

    /**
     * Check apakah bisa load less
     */
    public function canLoadLess(): bool
    {
        return $this->currentPage > 1;
    }

    public function render()
    {
        return view('livewire.kurir.pembayaran', [
            'hasMore' => $this->hasMore(),
            'canLoadLess' => $this->canLoadLess(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

#[Title('Pembayaran')]
#[Layout('components.layouts.mobile')]
class Pembayaran extends Component
{
    use Toast, WithPagination;

    public string $filter = 'all'; // unpaid, paid, all
    public string $search = '';

    /**
     * Get transaksi yang perlu konfirmasi pembayaran
     * - Hanya transaksi yang di-handle oleh kurir yang sedang login
     * - Filter berdasarkan payment_status
     */
    #[Computed]
    public function transactions(): Collection
    {
        $courier = Auth::guard('courier')->user();

        $query = Transaction::with(['customer', 'service', 'payments'])
            ->where('courier_motorcycle_id', $courier->id)
            ->whereNotNull('customer_id') // Pastikan customer ada
            ->whereNotNull('service_id')  // Pastikan service ada
            ->whereHas('customer')        // Pastikan customer tidak soft deleted
            ->whereHas('service')         // Pastikan service tidak soft deleted
            ->where(function ($q) {
                // Transaksi dengan payment_timing = 'on_pickup' dan sudah picked_up
                $q->where(function ($subQ) {
                    $subQ->where('payment_timing', 'on_pickup')
                        ->whereIn('workflow_status', ['picked_up', 'at_loading_post', 'in_washing', 'washing_completed', 'out_for_delivery', 'delivered']);
                })
                // Atau transaksi dengan payment_timing = 'on_delivery' dan sudah delivered
                ->orWhere(function ($subQ) {
                    $subQ->where('payment_timing', 'on_delivery')
                        ->whereIn('workflow_status', ['out_for_delivery', 'delivered']);
                });
            });

        // Filter berdasarkan payment_status
        if ($this->filter === 'unpaid') {
            $query->where('payment_status', 'unpaid');
        } elseif ($this->filter === 'paid') {
            $query->where('payment_status', 'paid');
        }

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return $query->orderBy('order_date', 'desc')
            ->get();
    }

    /**
     * Get statistik pembayaran
     */
    #[Computed]
    public function stats(): array
    {
        $courier = Auth::guard('courier')->user();

        $unpaidCount = Transaction::where('courier_motorcycle_id', $courier->id)
            ->where('payment_status', 'unpaid')
            ->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('payment_timing', 'on_pickup')
                        ->whereIn('workflow_status', ['picked_up', 'at_loading_post', 'in_washing', 'washing_completed', 'out_for_delivery', 'delivered']);
                })
                ->orWhere(function ($subQ) {
                    $subQ->where('payment_timing', 'on_delivery')
                        ->whereIn('workflow_status', ['out_for_delivery', 'delivered']);
                });
            })
            ->count();

        $paidCount = Transaction::where('courier_motorcycle_id', $courier->id)
            ->where('payment_status', 'paid')
            ->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('payment_timing', 'on_pickup')
                        ->whereIn('workflow_status', ['picked_up', 'at_loading_post', 'in_washing', 'washing_completed', 'out_for_delivery', 'delivered']);
                })
                ->orWhere(function ($subQ) {
                    $subQ->where('payment_timing', 'on_delivery')
                        ->whereIn('workflow_status', ['out_for_delivery', 'delivered']);
                });
            })
            ->count();

        $unpaidTotal = Transaction::where('courier_motorcycle_id', $courier->id)
            ->where('payment_status', 'unpaid')
            ->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('payment_timing', 'on_pickup')
                        ->whereIn('workflow_status', ['picked_up', 'at_loading_post', 'in_washing', 'washing_completed', 'out_for_delivery', 'delivered']);
                })
                ->orWhere(function ($subQ) {
                    $subQ->where('payment_timing', 'on_delivery')
                        ->whereIn('workflow_status', ['out_for_delivery', 'delivered']);
                });
            })
            ->sum('total_price');

        return [
            'unpaid_count' => $unpaidCount,
            'paid_count' => $paidCount,
            'unpaid_total' => $unpaidTotal,
        ];
    }

    public function render()
    {
        return view('livewire.kurir.pembayaran');
    }
}

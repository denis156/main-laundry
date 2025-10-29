<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use Carbon\Carbon;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

#[Title('Beranda Pelanggan')]
#[Layout('components.layouts.pelanggan')]
class Beranda extends Component
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
     * Get greeting based on time
     */
    #[Computed]
    public function greeting(): string
    {
        $hour = now()->hour;

        if ($hour >= 5 && $hour < 12) {
            return 'Selamat Pagi';
        } elseif ($hour >= 12 && $hour < 15) {
            return 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            return 'Selamat Sore';
        } else {
            return 'Selamat Malam';
        }
    }

    /**
     * Get today's date in Indonesian format
     */
    #[Computed]
    public function todayDate(): string
    {
        return Carbon::now()->translatedFormat('l, d F Y');
    }

    /**
     * Get total orders count (all transactions)
     */
    #[Computed]
    public function totalOrdersCount()
    {
        return Transaction::where('customer_id', $this->customer->id)->count();
    }

    /**
     * Get active orders with details (limit 5)
     */
    #[Computed]
    public function activeOrders()
    {
        return Transaction::where('customer_id', $this->customer->id)
            ->whereNotIn('workflow_status', ['completed', 'cancelled'])
            ->with(['service', 'courierMotorcycle'])
            ->orderBy('order_date', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get WhatsApp CS URL with pre-filled message
     */
    public function getWhatsAppCSUrl(): string
    {
        $customer = $this->customer;
        $customerName = $customer?->name ?? 'Pelanggan';
        $message = "Halo Admin Main Laundry, saya {$customerName}. Saya ingin bertanya tentang...";
        $encodedMessage = urlencode($message);
        $csPhone = config('sosmed.phone');

        return "https://wa.me/{$csPhone}?text={$encodedMessage}";
    }

    public function render()
    {
        return view('livewire.pelanggan.beranda');
    }
}

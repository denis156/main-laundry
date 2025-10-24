<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\CourierMotorcycle;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

#[Title('Beranda Kurir')]
#[Layout('components.layouts.mobile')]
class Beranda extends Component
{
    public $headers;

    #[Computed]
    public function courier()
    {
        return Auth::guard('courier')->user();
    }

    #[Computed]
    public function assignedPos()
    {
        $courier = $this->courier;
        if (!$courier || !$courier->assigned_pos_id) {
            return null;
        }
        return $courier->assignedPos;
    }

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

    #[Computed]
    public function todayDate(): string
    {
        return Carbon::now()->translatedFormat('l, d F Y');
    }

    #[Computed]
    public function currentMonth(): string
    {
        return Carbon::now()->translatedFormat('F Y');
    }

    #[Computed]
    public function leaders()
    {
        return CourierMotorcycle::withCount([
            'transactions' => function ($query) {
                $query->where('workflow_status', 'delivered')
                      ->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            }
        ])
        ->orderByDesc('transactions_count')
        ->take(5)
        ->get()
        ->map(function ($courier, $index) {
            $courier->rank = $index + 1;
            return $courier;
        });
    }

    public function getWhatsAppCSUrl(): string
    {
        $courier = $this->courier;
        $courierName = $courier?->name ?? 'Kurir';
        $posName = $this->assignedPos?->name ?? 'Pos tidak diketahui';
        $message = "Halo Admin Main Laundry, saya {$courierName} dari {$posName}. Saya ingin bertanya tentang...";
        $encodedMessage = urlencode($message);
        $csPhone = config('sosmed.phone');

        return "https://wa.me/{$csPhone}?text={$encodedMessage}";
    }

    public function mount()
    {
        // Header kolom tabel leaderboard
        $this->headers = [
            ['key' => 'rank', 'label' => 'Rank'],
            ['key' => 'name', 'label' => 'Nama'],
            ['key' => 'transactions_count', 'label' => 'Pengantaran'],
        ];
    }


    public function render()
    {
        return view('livewire.kurir.beranda');
    }
}

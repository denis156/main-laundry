<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Title('Beranda Kurir')]
#[Layout('components.layouts.mobile')]
class Beranda extends Component
{
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

    public function render()
    {
        return view('livewire.kurir.beranda');
    }
}

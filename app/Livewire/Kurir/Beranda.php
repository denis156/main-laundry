<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

#[Title('Beranda Kurir')]
#[Layout('components.layouts.mobile')]
class Beranda extends Component
{
    /**
     * Refresh dashboard - dipanggil dari JavaScript saat menerima broadcast event
     */
    #[On('refresh-dashboard')]
    public function refreshDashboard(): void
    {
        // Refresh computed properties untuk load data terbaru
        unset($this->pendingConfirmation);
        unset($this->pendingPickup);
        unset($this->completedTransactions);
        unset($this->pendingConfirmationTransactions);
    }
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
    public function pendingConfirmation()
    {
        return $this->courier?->transactions()
            ->where('workflow_status', 'pending_confirmation')
            ->count() ?? 0;
    }

    #[Computed]
    public function pendingPickup()
    {
        return $this->courier?->transactions()
            ->where('workflow_status', 'confirmed')
            ->count() ?? 0;
    }

    #[Computed]
    public function completedTransactions()
    {
        return $this->courier?->transactions()
            ->where('workflow_status', 'delivered')
            ->count() ?? 0;
    }

    #[Computed]
    public function pendingConfirmationTransactions(): Collection
    {
        $courier = Auth::guard('courier')->user();

        // Load pos dengan area layanan
        $assignedPos = $courier->assignedPos;

        $query = \App\Models\Transaction::with(['customer', 'service', 'pos'])
            ->where(function ($q) use ($courier) {
                // Transaksi yang sudah di-assign ke kurir ini
                $q->where('courier_motorcycle_id', $courier->id)
                    // ATAU transaksi yang belum ada kurirnya (bisa diambil)
                    ->orWhereNull('courier_motorcycle_id');
            })
            ->where('workflow_status', 'pending_confirmation')
            ->whereNotNull('customer_id')
            ->whereNotNull('service_id')
            ->whereHas('customer')
            ->whereHas('service');

        // Filter berdasarkan area layanan pos (hanya tampilkan transaksi yang customernya di area pos)
        if ($assignedPos && !empty($assignedPos->area)) {
            $query->whereHas('customer', function ($q) use ($assignedPos) {
                $q->where(function ($subQ) use ($assignedPos) {
                    // Customer village_name harus ada dalam pos.area (JSON array)
                    foreach ($assignedPos->area as $kelurahan) {
                        $subQ->orWhere('village_name', $kelurahan);
                    }
                    // ATAU customer belum ada village_name (backward compatibility)
                    $subQ->orWhereNull('village_name');
                });
            });
        }

        return $query->orderBy('order_date', 'desc')
            ->limit(5)
            ->get();
    }

    public function getWhatsAppCSUrl(): string
    {
        $courier = $this->courier;
        $courierName = $courier?->name ?? 'Kurir';
        $posName = $this->assignedPos?->name ?? 'Pos tidak diketahui';
        $message = "Halo Admin Main Laundry, saya {$courierName} dari {$posName}. Saya ingin bertanya tentang...";
        $encodedMessage = urlencode($message);
        $csPhone = config('app.cs_phone', '6281234567890');

        return "https://wa.me/{$csPhone}?text={$encodedMessage}";
    }

    public function render()
    {
        return view('livewire.kurir.beranda');
    }
}

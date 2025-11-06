<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Helper\StatusTransactionCustomerHelper;
use App\Models\Pos;

#[Title('Info')]
#[Layout('components.layouts.pelanggan')]
class Info extends Component
{
    // POS Load More properties
    public int $posLimit = 5;
    public int $posOffset = 0;

    /**
     * Check if CS WhatsApp is available
     */
    public function hasCSWhatsApp(): bool
    {
        $phone = config('sosmed.phone');
        return !empty($phone);
    }

    /**
     * Check if CS Email is available
     */
    public function hasCSEmail(): bool
    {
        $email = config('sosmed.email');
        return !empty($email);
    }

    /**
     * Generate WhatsApp URL untuk kontak CS
     */
    public function getWhatsAppCSUrl(): ?string
    {
        $phone = config('sosmed.phone');

        if (empty($phone)) {
            return null;
        }

        // Format nomor telepon (hapus karakter non-numeric)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Format nomor Indonesia untuk WhatsApp
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            $cleanPhone = '62' . $cleanPhone;
        } elseif (!str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        $customer = Auth::guard('customer')->user();
        $customerName = $customer?->name ?? 'Pelanggan';

        // Message template
        $message = "*Halo CS Main Laundry*, saya *{$customerName}* mau bertanya tentang layanan laundry...";
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    /**
     * Get customer-friendly workflow statuses with descriptions
     */
    public function getWorkflowStatuses(): array
    {
        return [
            [
                'code' => 'pending_confirmation',
                'label' => StatusTransactionCustomerHelper::getStatusText('pending_confirmation'),
                'badge' => StatusTransactionCustomerHelper::getStatusBadgeColor('pending_confirmation'),
                'description' => 'Pesanan sedang diverifikasi',
            ],
            [
                'code' => 'confirmed',
                'label' => StatusTransactionCustomerHelper::getStatusText('confirmed'),
                'badge' => StatusTransactionCustomerHelper::getStatusBadgeColor('confirmed'),
                'description' => 'Kurir akan menjemput',
            ],
            [
                'code' => 'picked_up',
                'label' => StatusTransactionCustomerHelper::getStatusText('picked_up'),
                'badge' => StatusTransactionCustomerHelper::getStatusBadgeColor('picked_up'),
                'description' => 'Pakaian menuju pencucian',
            ],
            [
                'code' => 'washing_completed',
                'label' => StatusTransactionCustomerHelper::getStatusText('washing_completed'),
                'badge' => StatusTransactionCustomerHelper::getStatusBadgeColor('washing_completed'),
                'description' => 'Cucian selesai, disiapkan untuk antar',
            ],
            [
                'code' => 'out_for_delivery',
                'label' => StatusTransactionCustomerHelper::getStatusText('out_for_delivery'),
                'badge' => StatusTransactionCustomerHelper::getStatusBadgeColor('out_for_delivery'),
                'description' => 'Cucian dalam pengantaran',
            ],
            [
                'code' => 'delivered',
                'label' => StatusTransactionCustomerHelper::getStatusText('delivered'),
                'badge' => StatusTransactionCustomerHelper::getStatusBadgeColor('delivered'),
                'description' => 'Cucian selesai dan dikembalikan',
            ],
            [
                'code' => 'cancelled',
                'label' => StatusTransactionCustomerHelper::getStatusText('cancelled'),
                'badge' => StatusTransactionCustomerHelper::getStatusBadgeColor('cancelled'),
                'description' => 'Pesanan dibatalkan',
            ],
        ];
    }

    /**
     * Get active services from database
     */
    #[Computed]
    public function services()
    {
        return \App\Models\Service::where('is_active', true)
            ->orderBy('duration_days', 'asc')
            ->get();
    }

    /**
     * Get active POS from database with incremental loading
     */
    public function getPosList()
    {
        return Pos::where('is_active', true)
            ->orderBy('name', 'asc')
            ->offset(0)
            ->take($this->posOffset + $this->posLimit)
            ->get();
    }

    /**
     * Get total count POS untuk load more
     */
    public function getTotalPos(): int
    {
        return Pos::where('is_active', true)->count();
    }

    /**
     * Check if has more POS data to load
     */
    public function getHasMorePos(): bool
    {
        $currentlyShowing = $this->posOffset + $this->posLimit;
        return $currentlyShowing < $this->getTotalPos();
    }

    /**
     * Check if can load less POS
     */
    public function getCanLoadLessPos(): bool
    {
        return $this->posOffset > 0;
    }

    /**
     * Load more POS data (incremental)
     */
    public function loadMorePos(): void
    {
        $this->posOffset += $this->posLimit;
    }

    /**
     * Load less POS data (decremental)
     */
    public function loadLessPos(): void
    {
        if ($this->posOffset > 0) {
            $this->posOffset = max(0, $this->posOffset - $this->posLimit);
        }
    }

    public function render()
    {
        return view('livewire.pelanggan.info');
    }
}

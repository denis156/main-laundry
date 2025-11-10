<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use App\Models\Location;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Helper\StatusTransactionHelper;
use App\Helper\Database\CourierHelper;
use App\Helper\Database\LocationHelper;

#[Title('Info')]
#[Layout('components.layouts.kurir')]
class Info extends Component
{
    /**
     * Get assigned location (pos/resort) for current courier
     */
    #[Computed]
    public function assignedPos()
    {
        $courier = Auth::guard('courier')->user();

        if (!$courier || !$courier->assigned_location_id) {
            return null;
        }

        return Location::with('transactions')->find($courier->assigned_location_id);
    }

    /**
     * Get PIC name dari assigned pos
     */
    #[Computed]
    public function posContactName(): ?string
    {
        $pos = $this->assignedPos;
        if (!$pos) {
            return null;
        }

        $contactInfo = LocationHelper::getContact($pos);
        return $contactInfo['pic_name'] ?? null;
    }

    /**
     * Get phone dari assigned pos
     */
    #[Computed]
    public function posContactPhone(): ?string
    {
        $pos = $this->assignedPos;
        if (!$pos) {
            return null;
        }

        $contactInfo = LocationHelper::getContact($pos);
        return $contactInfo['phone'] ?? null;
    }

    /**
     * Get address dari assigned pos
     */
    #[Computed]
    public function posAddress(): ?string
    {
        $pos = $this->assignedPos;
        if (!$pos) {
            return null;
        }

        return LocationHelper::getFullAddress($pos);
    }

    /**
     * Get coverage area dari assigned pos
     */
    #[Computed]
    public function posCoverageArea(): array
    {
        $pos = $this->assignedPos;
        if (!$pos) {
            return [];
        }

        return LocationHelper::getCoverageArea($pos);
    }

    /**
     * Get all workflow statuses with badge colors and descriptions
     */
    public function getWorkflowStatuses(): array
    {
        return [
            [
                'code' => 'pending_confirmation',
                'label' => StatusTransactionHelper::getStatusText('pending_confirmation'),
                'badge' => StatusTransactionHelper::getStatusBadgeColor('pending_confirmation'),
                'description' => 'Pesanan baru, belum diambil kurir'
            ],
            [
                'code' => 'confirmed',
                'label' => StatusTransactionHelper::getStatusText('confirmed'),
                'badge' => StatusTransactionHelper::getStatusBadgeColor('confirmed'),
                'description' => 'Sudah diambil, siap dijemput ke customer'
            ],
            [
                'code' => 'picked_up',
                'label' => StatusTransactionHelper::getStatusText('picked_up'),
                'badge' => StatusTransactionHelper::getStatusBadgeColor('picked_up'),
                'description' => 'Cucian sudah diambil, dalam perjalanan ke pos'
            ],
            [
                'code' => 'at_loading_post',
                'label' => StatusTransactionHelper::getStatusText('at_loading_post'),
                'badge' => StatusTransactionHelper::getStatusBadgeColor('at_loading_post'),
                'description' => 'Cucian sudah sampai di pos loading'
            ],
            [
                'code' => 'in_washing',
                'label' => StatusTransactionHelper::getStatusText('in_washing'),
                'badge' => StatusTransactionHelper::getStatusBadgeColor('in_washing'),
                'description' => 'Cucian sedang dalam proses pencucian'
            ],
            [
                'code' => 'washing_completed',
                'label' => StatusTransactionHelper::getStatusText('washing_completed'),
                'badge' => StatusTransactionHelper::getStatusBadgeColor('washing_completed'),
                'description' => 'Cucian sudah selesai, siap diantar ke customer'
            ],
            [
                'code' => 'out_for_delivery',
                'label' => StatusTransactionHelper::getStatusText('out_for_delivery'),
                'badge' => StatusTransactionHelper::getStatusBadgeColor('out_for_delivery'),
                'description' => 'Sedang dalam pengiriman ke customer'
            ],
            [
                'code' => 'delivered',
                'label' => StatusTransactionHelper::getStatusText('delivered'),
                'badge' => StatusTransactionHelper::getStatusBadgeColor('delivered'),
                'description' => 'Cucian sudah diterima customer'
            ],
            [
                'code' => 'cancelled',
                'label' => StatusTransactionHelper::getStatusText('cancelled'),
                'badge' => StatusTransactionHelper::getStatusBadgeColor('cancelled'),
                'description' => 'Pesanan dibatalkan'
            ],
        ];
    }

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

        $courier = Auth::guard('courier')->user();
        $courierName = $courier ? CourierHelper::getName($courier) : 'Kurir';
        $posName = $this->assignedPos?->name ?? 'Pos tidak diketahui';

        // Message template
        $message = "*Halo Admin Main Laundry*, saya *{$courierName}* dari *{$posName}*. Saya ingin bertanya tentang...";
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    /**
     * Generate WhatsApp URL untuk kontak Penanggung Jawab Pos
     */
    public function getWhatsAppPosUrl(): ?string
    {
        $pos = $this->assignedPos;

        if (!$pos) {
            return null;
        }

        $contactInfo = LocationHelper::getContact($pos);
        $phone = $contactInfo['phone'] ?? null;

        if (!$phone) {
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

        $courier = Auth::guard('courier')->user();
        $courierName = $courier ? CourierHelper::getName($courier) : 'Kurir';
        $picName = $contactInfo['pic_name'] ?? 'Admin';

        // Message template
        $message = "Halo {$picName}, saya {$courierName} dari {$pos->name}. Saya ingin bertanya tentang...";
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    public function render()
    {
        return view('livewire.kurir.info');
    }
}

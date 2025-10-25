<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use App\Models\Pos;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

#[Title('Info')]
#[Layout('components.layouts.kurir')]
class Info extends Component
{
    /**
     * Get assigned pos for current courier
     */
    #[Computed]
    public function assignedPos()
    {
        $courier = Auth::guard('courier')->user();

        if (!$courier || !$courier->assigned_pos_id) {
            return null;
        }

        return Pos::find($courier->assigned_pos_id);
    }

    /**
     * Generate WhatsApp URL untuk kontak CS
     */
    public function getWhatsAppCSUrl(): string
    {
        $phone = config('sosmed.phone');

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
        $courierName = $courier?->name ?? 'Kurir';
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

        if (!$pos || !$pos->phone) {
            return null;
        }

        // Format nomor telepon (hapus karakter non-numeric)
        $cleanPhone = preg_replace('/[^0-9]/', '', $pos->phone);

        // Format nomor Indonesia untuk WhatsApp
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            $cleanPhone = '62' . $cleanPhone;
        } elseif (!str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        $courier = Auth::guard('courier')->user();
        $courierName = $courier?->name ?? 'Kurir';

        // Message template
        $message = "Halo {$pos->pic_name}, saya {$courierName} dari {$pos->name}. Saya ingin bertanya tentang...";
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    public function render()
    {
        return view('livewire.kurir.info');
    }
}

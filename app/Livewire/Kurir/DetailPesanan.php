<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Title('Detail Pesanan')]
#[Layout('components.layouts.mobile')]
class DetailPesanan extends Component
{
    public Transaction $transaction;

    public function mount(int $id): void
    {
        $courier = Auth::guard('courier')->user();

        // Load transaction dengan semua relasi yang diperlukan
        $this->transaction = Transaction::with([
            'customer',
            'service',
            'pos',
            'courierMotorcycle',
            'payments'
        ])
            ->where('id', $id)
            ->where(function ($q) use ($courier) {
                // Hanya transaksi yang di-handle oleh kurir ini atau belum ada kurirnya
                $q->where('courier_motorcycle_id', $courier->id)
                    ->orWhereNull('courier_motorcycle_id');
            })
            ->firstOrFail();
    }

    /**
     * Generate WhatsApp URL dengan message untuk customer (pickup)
     */
    public function getWhatsAppUrl(): ?string
    {
        if (!$this->transaction->customer?->phone || !$this->transaction->customer?->name) {
            return null;
        }

        $courier = Auth::guard('courier')->user();

        // Format nomor telepon (hapus karakter non-numeric)
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->transaction->customer->phone);

        // Format nomor Indonesia untuk WhatsApp
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            $cleanPhone = '62' . $cleanPhone;
        } elseif (!str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        // Message template (pickup)
        $message = "Halo Kak *{$this->transaction->customer->name}*\n\n";
        $message .= "Perkenalkan, saya *{$courier->name}* dari *Main Laundry*. ";
        $message .= "Saya akan mengambil cucian Kakak hari ini.\n\n";
        $message .= "Boleh minta tolong kirim *share lokasi* Kakak ya, biar saya gak nyasar.\n\n";
        $message .= "Terima kasih";

        // Encode message untuk URL
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    /**
     * Generate WhatsApp URL dengan message untuk customer (delivery)
     */
    public function getWhatsAppUrlForDelivery(): ?string
    {
        if (!$this->transaction->customer?->phone || !$this->transaction->customer?->name) {
            return null;
        }

        $courier = Auth::guard('courier')->user();

        // Format nomor telepon (hapus karakter non-numeric)
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->transaction->customer->phone);

        // Format nomor Indonesia untuk WhatsApp
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            $cleanPhone = '62' . $cleanPhone;
        } elseif (!str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        // Message template untuk pengantaran
        $message = "Halo Kak *{$this->transaction->customer->name}*\n\n";
        $message .= "Kabar baik! Cucian Kakak sudah selesai dan siap diantar.\n\n";
        $message .= "Saya *{$courier->name}* dari *Main Laundry* akan mengantar cucian Kakak hari ini.\n\n";
        $message .= "Boleh minta tolong kirim *share lokasi* Kakak ya, biar saya gak nyasar.\n\n";
        $message .= "Terima kasih";

        // Encode message untuk URL
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    public function render()
    {
        return view('livewire.kurir.detail-pesanan');
    }
}

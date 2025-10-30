<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use Mary\Traits\Toast;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Title('Detail Pesanan')]
#[Layout('components.layouts.pelanggan')]
class DetailPesanan extends Component
{
    use Toast;

    public Transaction $transaction;

    // Modal states
    public bool $showCancelModal = false;

    public function mount(int $id = null): void
    {
        $customer = Auth::guard('customer')->user();

        // If no ID provided, get the latest active transaction
        if (!$id) {
            $this->transaction = Transaction::with([
                'customer',
                'service',
                'courierMotorcycle',
                'payments'
            ])
                ->where('customer_id', $customer->id)
                ->whereNotIn('workflow_status', ['delivered', 'cancelled'])
                ->orderBy('order_date', 'desc')
                ->firstOrFail();
        } else {
            // Load specific transaction
            $this->transaction = Transaction::with([
                'customer',
                'service',
                'courierMotorcycle',
                'payments'
            ])
                ->where('id', $id)
                ->where('customer_id', $customer->id)
                ->firstOrFail();
        }
    }

    /**
     * Get WhatsApp URL untuk Admin
     */
    public function getWhatsAppAdminUrl(): string
    {
        $customer = Auth::guard('customer')->user();
        $adminPhone = config('sosmed.phone');

        // Format nomor telepon
        $cleanPhone = preg_replace('/[^0-9]/', '', $adminPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        // Message template
        $message = "Halo Admin *Main Laundry*\n\n";
        $message .= "Saya *{$customer->name}* ingin menanyakan status pesanan saya.\n\n";
        $message .= "*Detail Pesanan:*\n";
        $message .= "• Invoice: {$this->transaction->invoice_number}\n";
        $message .= "• Layanan: {$this->transaction->service?->name}\n\n";
        $message .= "Terima kasih";

        $encodedMessage = urlencode($message);
        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    /**
     * Get WhatsApp URL untuk Kurir
     */
    public function getWhatsAppKurirUrl(): ?string
    {
        if (!$this->transaction->courierMotorcycle) {
            return null;
        }

        $customer = Auth::guard('customer')->user();
        $kurirPhone = $this->transaction->courierMotorcycle->phone;

        // Format nomor telepon
        $cleanPhone = preg_replace('/[^0-9]/', '', $kurirPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        // Message template
        $message = "Halo Kak *{$this->transaction->courierMotorcycle->name}*\n\n";
        $message .= "Saya *{$customer->name}* ingin menanyakan pesanan saya.\n\n";
        $message .= "*Detail Pesanan:*\n";
        $message .= "• Invoice: {$this->transaction->invoice_number}\n";
        $message .= "• Layanan: {$this->transaction->service?->name}\n\n";
        $message .= "Terima kasih";

        $encodedMessage = urlencode($message);
        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    /**
     * Open cancel modal
     */
    public function openCancelModal(): void
    {
        $this->showCancelModal = true;
    }

    /**
     * Cancel order
     */
    public function cancelOrder()
    {
        if ($this->transaction->workflow_status !== 'pending_confirmation') {
            $this->error(
                title: 'Tidak Dapat Dibatalkan!',
                description: 'Hanya pesanan dengan status pending yang bisa dibatalkan.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        $this->transaction->workflow_status = 'cancelled';
        $this->transaction->save();

        $this->success(
            title: 'Pesanan Dibatalkan!',
            description: 'Pesanan Anda berhasil dibatalkan.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
        $this->showCancelModal = false;

        // Redirect back to pesanan list
        return redirect()->route('pelanggan.pesanan');
    }

    public function render()
    {
        return view('livewire.pelanggan.detail-pesanan');
    }
}

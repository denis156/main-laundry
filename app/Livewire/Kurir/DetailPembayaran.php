<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Mary\Traits\Toast;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Title('Detail Pembayaran')]
#[Layout('components.layouts.mobile')]
class DetailPembayaran extends Component
{
    use Toast, WithFileUploads;

    public Transaction $transaction;

    // Bukti pembayaran untuk upload (jika belum ada)
    public $paymentProof;

    public function mount(int $id): void
    {
        $courier = Auth::guard('courier')->user();

        // Load transaction dengan semua relasi yang diperlukan
        $this->transaction = Transaction::with([
            'customer',
            'service',
            'pos',
            'courierMotorcycle',
        ])
            ->where('id', $id)
            ->where('courier_motorcycle_id', $courier->id)
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
            })
            ->firstOrFail();
    }

    /**
     * Upload bukti pembayaran untuk transaksi yang belum bayar
     */
    public function uploadPaymentProof(): void
    {
        // Validasi file
        if (empty($this->paymentProof)) {
            $this->error('Bukti pembayaran harus diupload!');
            return;
        }

        // Validasi transaksi belum bayar
        if ($this->transaction->payment_status === 'paid') {
            $this->error('Transaksi sudah lunas!');
            return;
        }

        // Upload file
        $filename = 'payment-proof-' . $this->transaction->invoice_number . '-' . time() . '.' . $this->paymentProof->getClientOriginalExtension();
        $path = $this->paymentProof->storeAs('payment-proofs', $filename, 'public');

        // Update transaction
        $this->transaction->update([
            'payment_proof_url' => $path,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->success('Bukti pembayaran berhasil diupload! Status pembayaran telah diperbarui.');

        // Clear inputs
        $this->paymentProof = null;

        $this->transaction->refresh();
    }

    /**
     * Get URL WhatsApp untuk mengingatkan customer bayar
     */
    public function getWhatsAppReminderUrl(): ?string
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

        // Format harga dan detail
        $totalPrice = number_format((float) $this->transaction->total_price, 0, ',', '.');
        $serviceName = $this->transaction->service?->name ?? 'Layanan';
        $invoiceNumber = $this->transaction->invoice_number;
        $weight = $this->transaction->weight;

        // Message template pengingat pembayaran
        $message = "Halo Kak *{$this->transaction->customer->name}*\n\n";
        $message .= "Ini pengingat pembayaran dari *Main Laundry*.\n\n";
        $message .= "*Detail Pesanan:*\n";
        $message .= "• Invoice: {$invoiceNumber}\n";
        $message .= "• Layanan: {$serviceName}\n";
        $message .= "• Berat: {$weight} kg\n";
        $message .= "• Total Tagihan: Rp {$totalPrice}\n";
        $message .= "• Status: *Belum Lunas*\n\n";
        $message .= "Mohon segera melakukan pembayaran ya Kak. Pembayaran dapat dilakukan via QRIS atau transfer bank.\n\n";
        $message .= "Terima kasih";

        // Encode message untuk URL
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    public function render()
    {
        // Refresh transaction data dari database setiap render (termasuk saat polling)
        $this->transaction->refresh();
        $this->transaction->load([
            'customer',
            'service',
            'pos',
            'courierMotorcycle',
        ]);

        return view('livewire.kurir.detail-pembayaran');
    }
}

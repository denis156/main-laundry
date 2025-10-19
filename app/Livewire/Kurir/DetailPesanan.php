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

#[Title('Detail Pesanan')]
#[Layout('components.layouts.mobile')]
class DetailPesanan extends Component
{
    use Toast, WithFileUploads;

    public Transaction $transaction;

    // Berat untuk input (jika status confirmed)
    public ?float $weight = null;

    // Bukti pembayaran untuk upload
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
            // 081234567890 -> 6281234567890
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            // 81234567890 -> 6281234567890
            $cleanPhone = '62' . $cleanPhone;
        } elseif (!str_starts_with($cleanPhone, '62')) {
            // Jika tidak dimulai dengan 62, 0, atau 8, tambahkan 62
            $cleanPhone = '62' . $cleanPhone;
        }

        // Format harga
        $pricePerKg = number_format((float) $this->transaction->price_per_kg, 0, ',', '.');
        $serviceName = $this->transaction->service?->name ?? 'Layanan';
        $invoiceNumber = $this->transaction->invoice_number;
        $customerAddress = $this->transaction->customer?->address ?? 'Alamat belum tersedia';

        // Message template dengan info layanan dan harga
        $message = "Halo Kak *{$this->transaction->customer->name}*\n\n";
        $message .= "Perkenalkan, saya *{$courier->name}* dari *Main Laundry*. ";
        $message .= "Saya akan mengambil cucian Kakak hari ini.\n\n";
        $message .= "*Detail Pesanan:*\n";
        $message .= "• Invoice: {$invoiceNumber}\n";
        $message .= "• Layanan: {$serviceName}\n";
        $message .= "• Harga: Rp {$pricePerKg}/kg\n\n";
        $message .= "*Alamat Penjemputan:*\n";
        $message .= "{$customerAddress}\n\n";
        $message .= "Nanti cucian akan ditimbang terlebih dahulu ya Kak.\n\n";
        $message .= "Boleh minta tolong kirim *share lokasi* Kakak ya, agar saya bisa sampai dengan tepat.\n\n";
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
            // 081234567890 -> 6281234567890
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            // 81234567890 -> 6281234567890
            $cleanPhone = '62' . $cleanPhone;
        } elseif (!str_starts_with($cleanPhone, '62')) {
            // Jika tidak dimulai dengan 62, 0, atau 8, tambahkan 62
            $cleanPhone = '62' . $cleanPhone;
        }

        // Format harga dan detail
        $totalPrice = number_format((float) $this->transaction->total_price, 0, ',', '.');
        $pricePerKg = number_format((float) $this->transaction->price_per_kg, 0, ',', '.');
        $weight = $this->transaction->weight;
        $serviceName = $this->transaction->service?->name ?? 'Layanan';
        $invoiceNumber = $this->transaction->invoice_number;
        $isPaid = $this->transaction->payment_status === 'paid';
        $paymentTiming = $this->transaction->payment_timing === 'on_delivery' ? 'Bayar Saat Antar' : 'Bayar Saat Jemput';
        $customerAddress = $this->transaction->customer?->address ?? 'Alamat belum tersedia';

        // Message template untuk pengantaran dengan detail lengkap
        $message = "Halo Kak *{$this->transaction->customer->name}*\n\n";
        $message .= "Kabar baik! Cucian Kakak sudah selesai dan siap diantar.\n\n";
        $message .= "Saya *{$courier->name}* dari *Main Laundry* akan mengantar cucian Kakak hari ini.\n\n";
        $message .= "*Detail Pesanan:*\n";
        $message .= "• Invoice: {$invoiceNumber}\n";
        $message .= "• Layanan: {$serviceName}\n";
        $message .= "• Harga: Rp {$pricePerKg}/kg\n";
        $message .= "• Berat: {$weight} kg\n";
        $message .= "• Pembayaran: {$paymentTiming}\n";

        if ($isPaid) {
            $message .= "• Status: *Sudah Lunas*\n";
            $message .= "• Total Tagihan: Rp {$totalPrice}\n\n";
        } else {
            $message .= "• Status: *Belum Lunas*\n";
            $message .= "• Total Tagihan: Rp {$totalPrice}\n\n";
            $message .= "Pembayaran bisa dilakukan saat pengantaran via QRIS.\n\n";
        }

        $message .= "*Alamat Pengantaran:*\n";
        $message .= "{$customerAddress}\n\n";
        $message .= "Boleh minta tolong kirim *share lokasi* Kakak ya, agar saya bisa sampai dengan tepat.\n\n";
        $message .= "Terima kasih";

        // Encode message untuk URL
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    /**
     * Konfirmasi dan ambil pesanan (ubah status dari pending_confirmation ke confirmed)
     */
    public function confirmOrder(): void
    {
        $courier = Auth::guard('courier')->user();
        $assignedPos = $courier->assignedPos;

        if ($this->transaction->workflow_status !== 'pending_confirmation') {
            $this->error('Pesanan tidak bisa dikonfirmasi dengan status saat ini.');
            return;
        }

        // Validasi area layanan
        if ($assignedPos && !empty($assignedPos->area)) {
            $customerVillage = $this->transaction->customer?->village_name;
            $isInArea = in_array($customerVillage, $assignedPos->area) || empty($customerVillage);

            if (!$isInArea) {
                $this->error('Pesanan di luar area layanan Anda.');
                return;
            }
        }

        $this->transaction->update([
            'courier_motorcycle_id' => $courier->id,
            'workflow_status' => 'confirmed',
        ]);

        $this->success('Pesanan berhasil diambil dan dikonfirmasi!');
        $this->transaction->refresh();
    }

    /**
     * Batalkan pesanan (ubah status dari pending_confirmation ke cancelled)
     * Assign kurir ke transaksi untuk tracking siapa yang membatalkan
     */
    public function cancelOrder(): void
    {
        $courier = Auth::guard('courier')->user();

        if ($this->transaction->workflow_status !== 'pending_confirmation') {
            $this->error('Pesanan tidak bisa dibatalkan dengan status saat ini.');
            return;
        }

        // Assign kurir ke transaksi untuk tracking siapa yang membatalkan
        $this->transaction->update([
            'courier_motorcycle_id' => $courier->id,
            'workflow_status' => 'cancelled',
        ]);

        $this->success('Pesanan berhasil dibatalkan.');
        $this->transaction->refresh();
    }

    /**
     * Tandai pesanan sudah dijemput (ubah status dari confirmed ke picked_up)
     */
    public function markAsPickedUp(): void
    {
        $courier = Auth::guard('courier')->user();

        if ($this->transaction->workflow_status !== 'confirmed') {
            $this->error('Pesanan tidak bisa diupdate dengan status saat ini.');
            return;
        }

        // Validasi berat harus diisi
        if (empty($this->weight) || $this->weight <= 0) {
            $this->error('Berat cucian harus diisi dan lebih dari 0 kg!');
            return;
        }

        // Validasi bukti pembayaran jika bayar saat jemput
        if ($this->transaction->payment_timing === 'on_pickup') {
            if (empty($this->paymentProof)) {
                $this->error('Bukti pembayaran harus diupload untuk pesanan yang bayar saat jemput!');
                return;
            }
        }

        $pricePerKg = $this->transaction->price_per_kg;
        $totalPrice = $this->weight * $pricePerKg;

        $updateData = [
            'workflow_status' => 'picked_up',
            'pos_id' => $courier->assigned_pos_id,
            'weight' => $this->weight,
            'total_price' => $totalPrice,
        ];

        // Handle upload bukti pembayaran jika bayar saat jemput
        if ($this->transaction->payment_timing === 'on_pickup' && !empty($this->paymentProof)) {
            $filename = 'payment-proof-' . $this->transaction->invoice_number . '-' . time() . '.' . $this->paymentProof->getClientOriginalExtension();
            $path = $this->paymentProof->storeAs('payment-proofs', $filename, 'public');

            $updateData['payment_proof_url'] = $path;
            $updateData['payment_status'] = 'paid';
            $updateData['paid_at'] = now();
        }

        $this->transaction->update($updateData);

        $message = 'Pesanan berhasil ditandai sudah dijemput dengan berat ' . $this->weight . ' kg!';
        if ($this->transaction->payment_timing === 'on_pickup') {
            $message .= ' Pembayaran telah terkonfirmasi.';
        }

        $this->success($message);

        // Clear inputs
        $this->weight = null;
        $this->paymentProof = null;

        $this->transaction->refresh();
    }

    /**
     * Get hint text untuk total harga berdasarkan berat yang diinput
     */
    public function getTotalPriceHint(): string
    {
        if (empty($this->weight) || $this->weight <= 0 || $this->transaction->price_per_kg <= 0) {
            return 'Masukkan berat untuk melihat total harga';
        }

        $totalPrice = $this->weight * $this->transaction->price_per_kg;

        return 'Total: Rp ' . number_format($totalPrice, 0, ',', '.');
    }

    /**
     * Tandai pesanan sudah di pos (ubah status dari picked_up ke at_loading_post)
     */
    public function markAsAtLoadingPost(): void
    {
        if ($this->transaction->workflow_status !== 'picked_up') {
            $this->error('Pesanan tidak bisa diupdate dengan status saat ini.');
            return;
        }

        $this->transaction->update([
            'workflow_status' => 'at_loading_post',
        ]);

        $this->success('Pesanan berhasil ditandai sudah di pos loading!');
        $this->transaction->refresh();
    }

    /**
     * Tandai pesanan dalam pengiriman (ubah status dari washing_completed ke out_for_delivery)
     */
    public function markAsOutForDelivery(): void
    {
        if ($this->transaction->workflow_status !== 'washing_completed') {
            $this->error('Pesanan tidak bisa diupdate dengan status saat ini.');
            return;
        }

        $this->transaction->update([
            'workflow_status' => 'out_for_delivery',
        ]);

        $this->success('Pesanan berhasil ditandai dalam pengiriman!');
        $this->transaction->refresh();
    }

    /**
     * Tandai pesanan terkirim (ubah status dari out_for_delivery ke delivered)
     */
    public function markAsDelivered(): void
    {
        if ($this->transaction->workflow_status !== 'out_for_delivery') {
            $this->error('Pesanan tidak bisa diupdate dengan status saat ini.');
            return;
        }

        // Validasi bukti pembayaran jika bayar saat antar
        if ($this->transaction->payment_timing === 'on_delivery') {
            if (empty($this->paymentProof)) {
                $this->error('Bukti pembayaran harus diupload untuk pesanan yang bayar saat antar!');
                return;
            }
        }

        $updateData = [
            'workflow_status' => 'delivered',
        ];

        // Handle upload bukti pembayaran jika bayar saat antar
        if ($this->transaction->payment_timing === 'on_delivery' && !empty($this->paymentProof)) {
            $filename = 'payment-proof-' . $this->transaction->invoice_number . '-' . time() . '.' . $this->paymentProof->getClientOriginalExtension();
            $path = $this->paymentProof->storeAs('payment-proofs', $filename, 'public');

            $updateData['payment_proof_url'] = $path;
            $updateData['payment_status'] = 'paid';
            $updateData['paid_at'] = now();
        }

        $this->transaction->update($updateData);

        $message = 'Pesanan berhasil ditandai terkirim!';
        if ($this->transaction->payment_timing === 'on_delivery') {
            $message .= ' Pembayaran telah terkonfirmasi.';
        }

        $this->success($message);

        // Clear inputs
        $this->paymentProof = null;

        $this->transaction->refresh();
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
            'payments'
        ]);

        return view('livewire.kurir.detail-pesanan');
    }
}

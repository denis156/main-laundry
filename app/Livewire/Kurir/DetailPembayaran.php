<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Mary\Traits\Toast;
use App\Models\Payment;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Helper\Database\PaymentHelper;

#[Title('Detail Pembayaran')]
#[Layout('components.layouts.kurir')]
class DetailPembayaran extends Component
{
    use Toast, WithFileUploads;

    public Payment $payment;
    public Transaction $transaction;

    // Bukti pembayaran untuk upload
    public $paymentProof;

    // Modal state
    public bool $showUploadModal = false;

    public function mount(int $id): void
    {
        $courier = Auth::guard('courier')->user();

        // Load payment dengan transaction dan relasi lainnya
        // $id di sini adalah payment_id, bukan transaction_id
        $this->payment = Payment::with([
            'transaction.customer',
            'transaction.location',
            'transaction.courier',
            'courier',
        ])
            ->where('id', $id)
            ->where('courier_id', $courier->id)
            ->firstOrFail();

        // Set transaction dari payment
        $this->transaction = $this->payment->transaction;
    }

    /**
     * Get bukti pembayaran URL dari JSONB
     */
    #[Computed]
    public function paymentProofUrl(): ?string
    {
        $payment = $this->payment;
        if (!$payment) {
            return null;
        }

        return PaymentHelper::getProofUrl($payment);
    }

    /**
     * Check apakah sudah ada bukti pembayaran
     */
    #[Computed]
    public function hasPaymentProof(): bool
    {
        return !empty($this->paymentProofUrl);
    }

    /**
     * Buka modal konfirmasi untuk upload bukti pembayaran
     */
    public function openUploadModal(): void
    {
        $this->showUploadModal = true;
    }

    /**
     * Upload bukti pembayaran ke Payment record
     * Auto-update payment_status jadi paid setelah upload bukti
     */
    public function uploadPaymentProof(): void
    {
        // Validasi file
        if (empty($this->paymentProof)) {
            $this->error(
                title: 'Bukti Pembayaran Kosong!',
                description: 'Anda harus upload bukti pembayaran terlebih dahulu.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Payment sudah di-load di mount(), jadi tinggal pakai
        // Upload file
        $filename = 'payment-proof-' . $this->transaction->invoice_number . '-' . time() . '.' . $this->paymentProof->getClientOriginalExtension();
        $path = $this->paymentProof->storeAs('payment-proofs', $filename, 'public');

        // Update payment record dengan bukti pembayaran ke JSONB field
        $data = $this->payment->data ?? [];
        $data['proof_url'] = $path;

        $this->payment->update([
            'data' => $data,
        ]);

        // Update payment_status jadi paid karena sudah ada bukti pembayaran
        $this->transaction->update([
            'payment_status' => 'paid',
        ]);

        $this->success(
            title: 'Bukti Berhasil Diupload!',
            description: 'Bukti pembayaran berhasil disimpan.',
            position: 'toast-top toast-end',
            timeout: 3000
        );

        // Clear inputs
        $this->paymentProof = null;
        $this->showUploadModal = false;

        // Refresh both
        $this->payment->refresh();
        $this->transaction->refresh();
    }

    public function render()
    {
        // Refresh transaction data dari database setiap render (termasuk saat polling)
        $this->transaction->refresh();
        $this->transaction->load([
            'customer',
            'location',
            'courier',
            'payments',
        ]);

        return view('livewire.kurir.detail-pembayaran');
    }
}

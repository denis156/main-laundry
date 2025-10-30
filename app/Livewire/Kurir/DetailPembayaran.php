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

#[Title('Detail Pembayaran')]
#[Layout('components.layouts.kurir')]
class DetailPembayaran extends Component
{
    use Toast, WithFileUploads;

    public Transaction $transaction;

    // Bukti pembayaran untuk upload
    public $paymentProof;

    // Modal state
    public bool $showUploadModal = false;

    public function mount(int $id): void
    {
        $courier = Auth::guard('courier')->user();

        // Load transaction dengan semua relasi yang diperlukan
        $this->transaction = Transaction::with([
            'customer',
            'service',
            'pos',
            'courierMotorcycle',
            'payments',
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
     * Get first payment untuk transaksi ini
     */
    #[Computed]
    public function payment(): ?Payment
    {
        return $this->transaction->payments->first();
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

        // Cari payment record untuk transaksi ini
        $payment = Payment::where('transaction_id', $this->transaction->id)->first();

        if (!$payment) {
            $this->error(
                title: 'Record Pembayaran Belum Ada!',
                description: 'Payment akan otomatis dibuat saat status berubah.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Upload file
        $filename = 'payment-proof-' . $this->transaction->invoice_number . '-' . time() . '.' . $this->paymentProof->getClientOriginalExtension();
        $path = $this->paymentProof->storeAs('payment-proofs', $filename, 'public');

        // Update payment record dengan bukti pembayaran
        $payment->update([
            'payment_proof_url' => $path,
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
            'payments',
        ]);

        return view('livewire.kurir.detail-pembayaran');
    }
}

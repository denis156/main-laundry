<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Payment;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

#[Title('Pembayaran')]
#[Layout('components.layouts.kurir')]
class Pembayaran extends Component
{
    use Toast, WithPagination, WithFileUploads;

    public string $filter = 'all'; // all, with_proof, without_proof
    public string $search = '';

    // Array untuk menyimpan file upload per payment ID
    public array $paymentProofs = [];

    // Modal state
    public bool $showUploadModal = false;

    // ID payment yang akan diproses
    public ?int $selectedPaymentId = null;

    /**
     * Get semua payment yang di-handle oleh kurir yang sedang login
     * - Tampilkan semua payment records
     * - Filter berdasarkan ada tidaknya bukti pembayaran
     */
    #[Computed]
    public function payments(): Collection
    {
        $courier = Auth::guard('courier')->user();

        $query = Payment::with(['transaction.customer', 'transaction.service', 'courierMotorcycle'])
            ->where('courier_motorcycle_id', $courier->id)
            ->whereHas('transaction', function ($q) {
                $q->whereNotNull('customer_id')
                    ->whereNotNull('service_id')
                    ->whereHas('customer')
                    ->whereHas('service');
            });

        // Filter berdasarkan bukti pembayaran
        if ($this->filter === 'with_proof') {
            $query->whereNotNull('payment_proof_url');
        } elseif ($this->filter === 'without_proof') {
            $query->whereNull('payment_proof_url');
        }

        // Search by invoice atau customer name
        if (!empty($this->search)) {
            $query->whereHas('transaction', function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return $query->orderBy('payment_date', 'desc')
            ->get();
    }

    /**
     * Buka modal konfirmasi untuk upload bukti pembayaran
     */
    public function openUploadModal(int $paymentId): void
    {
        $this->selectedPaymentId = $paymentId;
        $this->showUploadModal = true;
    }

    /**
     * Upload bukti pembayaran untuk payment tertentu
     */
    public function uploadPaymentProof(): void
    {
        // Validasi file
        if (empty($this->paymentProofs[$this->selectedPaymentId])) {
            $this->error('Bukti pembayaran harus diupload!');
            return;
        }

        // Cari payment record
        $payment = Payment::find($this->selectedPaymentId);

        if (!$payment) {
            $this->error('Payment tidak ditemukan!');
            return;
        }

        // Validasi ownership
        $courier = Auth::guard('courier')->user();
        if ($payment->courier_motorcycle_id !== $courier->id) {
            $this->error('Anda tidak memiliki akses untuk upload bukti pembayaran ini!');
            return;
        }

        // Upload file
        $filename = 'payment-proof-' . $payment->transaction->invoice_number . '-' . time() . '.' . $this->paymentProofs[$this->selectedPaymentId]->getClientOriginalExtension();
        $path = $this->paymentProofs[$this->selectedPaymentId]->storeAs('payment-proofs', $filename, 'public');

        // Update payment record dengan bukti pembayaran
        $payment->update([
            'payment_proof_url' => $path,
        ]);

        // Update payment_status jadi paid karena sudah ada bukti pembayaran
        $payment->transaction->update([
            'payment_status' => 'paid',
        ]);

        $this->success('Bukti pembayaran berhasil diupload!');

        // Clear input untuk payment ini
        unset($this->paymentProofs[$this->selectedPaymentId]);

        // Close modal dan reset selected payment
        $this->showUploadModal = false;
        $this->selectedPaymentId = null;

        // Refresh data
        unset($this->payments);
    }

    public function render()
    {
        // Initialize paymentProofs array untuk semua payment IDs
        // Ini penting agar Livewire tidak error saat bind wire:model dengan dynamic key
        foreach ($this->payments as $payment) {
            if (!isset($this->paymentProofs[$payment->id])) {
                $this->paymentProofs[$payment->id] = null;
            }
        }

        return view('livewire.kurir.pembayaran');
    }
}

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
use App\Helper\Database\PaymentHelper;

#[Title('Pembayaran')]
#[Layout('components.layouts.kurir')]
class Pembayaran extends Component
{
    use Toast, WithPagination, WithFileUploads;

    public string $filter = 'all'; // all, with_proof, without_proof
    public string $search = '';

    // Pagination manual
    public int $perPage = 5;
    public int $currentPage = 1;

    // Array untuk menyimpan file upload per payment ID
    public array $paymentProofs = [];

    // Modal state
    public bool $showUploadModal = false;

    // ID payment yang akan diproses
    public ?int $selectedPaymentId = null;

    /**
     * Get total count payment untuk pagination
     */
    #[Computed]
    public function totalPayments(): int
    {
        $courier = Auth::guard('courier')->user();

        $query = Payment::with(['transaction.customer', 'transaction.location', 'courier'])
            ->where('courier_id', $courier->id)
            ->whereHas('transaction', function ($q) {
                $q->whereNotNull('customer_id')
                    ->whereHas('customer');
            });

        // Filter berdasarkan bukti pembayaran (di JSONB)
        if ($this->filter === 'with_proof') {
            $query->whereRaw("data->>'proof_url' IS NOT NULL")
                  ->whereRaw("data->>'proof_url' != ''");
        } elseif ($this->filter === 'without_proof') {
            $query->where(function ($q) {
                $q->whereRaw("data->>'proof_url' IS NULL")
                  ->orWhereRaw("data->>'proof_url' = ''");
            });
        }

        // Search by invoice atau customer name (di JSONB)
        if (!empty($this->search)) {
            $query->whereHas('transaction', function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($subQ) {
                        $subQ->whereRaw("data->>'name' ILIKE ?", ['%' . $this->search . '%']);
                    });
            });
        }

        return $query->count();
    }

    /**
     * Get semua payment yang di-handle oleh kurir yang sedang login
     * - Tampilkan payment records dengan pagination manual
     * - Filter berdasarkan ada tidaknya bukti pembayaran
     */
    #[Computed]
    public function payments(): Collection
    {
        $courier = Auth::guard('courier')->user();

        $query = Payment::with(['transaction.customer', 'transaction.location', 'courier'])
            ->where('courier_id', $courier->id)
            ->whereHas('transaction', function ($q) {
                $q->whereNotNull('customer_id')
                    ->whereHas('customer');
            });

        // Filter berdasarkan bukti pembayaran (di JSONB)
        if ($this->filter === 'with_proof') {
            $query->whereRaw("data->>'proof_url' IS NOT NULL")
                  ->whereRaw("data->>'proof_url' != ''");
        } elseif ($this->filter === 'without_proof') {
            $query->where(function ($q) {
                $q->whereRaw("data->>'proof_url' IS NULL")
                  ->orWhereRaw("data->>'proof_url' = ''");
            });
        }

        // Search by invoice atau customer name (di JSONB)
        if (!empty($this->search)) {
            $query->whereHas('transaction', function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($subQ) {
                        $subQ->whereRaw("data->>'name' ILIKE ?", ['%' . $this->search . '%']);
                    });
            });
        }

        // Hitung limit berdasarkan currentPage
        $limit = $this->perPage * $this->currentPage;

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Tampilkan lebih banyak data
     */
    public function loadMore(): void
    {
        $this->currentPage++;
        unset($this->payments);
        unset($this->totalPayments);
    }

    /**
     * Tampilkan lebih sedikit data
     */
    public function loadLess(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            unset($this->payments);
            unset($this->totalPayments);
        }
    }

    /**
     * Check apakah masih ada data lagi
     */
    public function hasMore(): bool
    {
        return ($this->perPage * $this->currentPage) < $this->totalPayments;
    }

    /**
     * Check apakah bisa load less
     */
    public function canLoadLess(): bool
    {
        return $this->currentPage > 1;
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
            $this->error(
                title: 'Bukti Pembayaran Kosong!',
                description: 'Anda harus upload bukti pembayaran terlebih dahulu.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Cari payment record
        $payment = Payment::find($this->selectedPaymentId);

        if (!$payment) {
            $this->error(
                title: 'Payment Tidak Ditemukan!',
                description: 'Data payment tidak ditemukan di sistem.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Validasi ownership
        $courier = Auth::guard('courier')->user();
        if ($payment->courier_id !== $courier->id) {
            $this->error(
                title: 'Akses Ditolak!',
                description: 'Anda tidak memiliki akses untuk upload bukti pembayaran ini.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Upload file
        $filename = 'payment-proof-' . $payment->transaction->invoice_number . '-' . time() . '.' . $this->paymentProofs[$this->selectedPaymentId]->getClientOriginalExtension();
        $path = $this->paymentProofs[$this->selectedPaymentId]->storeAs('payment-proofs', $filename, 'public');

        // Update payment record dengan bukti pembayaran ke JSONB field
        $data = $payment->data ?? [];
        $data['proof_url'] = $path;

        $payment->update([
            'data' => $data,
        ]);

        // Update payment_status jadi paid karena sudah ada bukti pembayaran
        $payment->transaction->update([
            'payment_status' => 'paid',
        ]);

        $this->success(
            title: 'Bukti Berhasil Diupload!',
            description: 'Bukti pembayaran berhasil disimpan.',
            position: 'toast-top toast-end',
            timeout: 3000
        );

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

        return view('livewire.kurir.pembayaran', [
            'hasMore' => $this->hasMore(),
            'canLoadLess' => $this->canLoadLess(),
        ]);
    }
}

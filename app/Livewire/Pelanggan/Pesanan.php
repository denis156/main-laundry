<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use Mary\Traits\Toast;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

#[Title('Pesanan Saya')]
#[Layout('components.layouts.pelanggan')]
class Pesanan extends Component
{
    use Toast;

    public string $filter = 'all';
    public string $search = '';

    // Pagination
    public int $perPage = 5;
    public int $currentPage = 1;

    // Modal states
    public bool $showCancelModal = false;
    public ?int $selectedTransactionId = null;

    /**
     * Get total count transaksi untuk pagination
     */
    #[Computed]
    public function totalTransactions(): int
    {
        $customer = Auth::guard('customer')->user();

        $query = Transaction::where('customer_id', $customer->id);

        // Filter by status
        if ($this->filter !== 'all') {
            $query->where('workflow_status', $this->filter);
        }

        // Search
        if (!empty($this->search)) {
            $query->where('invoice_number', 'like', '%' . $this->search . '%');
        }

        return $query->count();
    }

    /**
     * Get transaksi pelanggan
     */
    #[Computed]
    public function transactions(): Collection
    {
        $customer = Auth::guard('customer')->user();

        $query = Transaction::with(['courier', 'location'])
            ->where('customer_id', $customer->id);

        // Filter by status
        if ($this->filter !== 'all') {
            $query->where('workflow_status', $this->filter);
        }

        // Search
        if (!empty($this->search)) {
            $query->where('invoice_number', 'like', '%' . $this->search . '%');
        }

        return $query->orderBy('created_at', 'desc')
            ->skip(($this->currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();
    }

    /**
     * Refresh orders when notified via Echo
     */
    #[On('refresh-orders')]
    public function refreshOrders(): void
    {
        unset($this->transactions);
        unset($this->totalTransactions);
    }

    /**
     * Reset ke halaman 1 saat filter/search berubah
     */
    public function updated($property): void
    {
        if (in_array($property, ['filter', 'search'])) {
            $this->currentPage = 1;
            unset($this->transactions);
            unset($this->totalTransactions);
        }
    }

    /**
     * Load more data
     */
    public function loadMore(): void
    {
        $this->currentPage++;
        unset($this->transactions);
        unset($this->totalTransactions);
    }

    /**
     * Load less data
     */
    public function loadLess(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            unset($this->transactions);
            unset($this->totalTransactions);
        }
    }

    /**
     * Check if has more data
     */
    #[Computed]
    public function hasMore(): bool
    {
        return ($this->perPage * $this->currentPage) < $this->totalTransactions;
    }

    /**
     * Check if can load less
     */
    #[Computed]
    public function canLoadLess(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Get WhatsApp URL untuk CS/Admin
     */
    public function getWhatsAppAdminUrl(Transaction $transaction): string
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

        $customerName = \App\Helper\Database\CustomerHelper::getName($customer);
        $items = \App\Helper\Database\TransactionHelper::getItems($transaction);
        $serviceNames = array_map(fn($item) => $item['service_name'] ?? 'N/A', $items);

        // Message template
        $message = "Halo Admin *Main Laundry*\n\n";
        $message .= "Saya *{$customerName}* ingin menanyakan status pesanan saya.\n\n";
        $message .= "*Detail Pesanan:*\n";
        $message .= "• Invoice: {$transaction->invoice_number}\n";
        $message .= "• Layanan: " . implode(', ', $serviceNames) . "\n\n";
        $message .= "Terima kasih";

        $encodedMessage = urlencode($message);
        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    /**
     * Get WhatsApp URL untuk Kurir
     */
    public function getWhatsAppKurirUrl(Transaction $transaction): ?string
    {
        if (!$transaction->courier) {
            return null;
        }

        $customer = Auth::guard('customer')->user();
        $customerName = \App\Helper\Database\CustomerHelper::getName($customer);
        $courierName = \App\Helper\Database\CourierHelper::getName($transaction->courier);
        $courierPhone = \App\Helper\Database\CourierHelper::getPhone($transaction->courier);

        // Format nomor telepon
        $cleanPhone = preg_replace('/[^0-9]/', '', $courierPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        $items = \App\Helper\Database\TransactionHelper::getItems($transaction);
        $serviceNames = array_map(fn($item) => $item['service_name'] ?? 'N/A', $items);

        // Message template
        $message = "Halo Kak *{$courierName}*\n\n";
        $message .= "Saya *{$customerName}* ingin menanyakan pesanan saya.\n\n";
        $message .= "*Detail Pesanan:*\n";
        $message .= "• Invoice: {$transaction->invoice_number}\n";
        $message .= "• Layanan: " . implode(', ', $serviceNames) . "\n\n";
        $message .= "Terima kasih";

        $encodedMessage = urlencode($message);
        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    /**
     * Open cancel modal
     */
    public function openCancelModal(int $transactionId): void
    {
        $this->selectedTransactionId = $transactionId;
        $this->showCancelModal = true;
    }

    /**
     * Cancel order
     */
    public function cancelOrder(): void
    {
        if (!$this->selectedTransactionId) {
            return;
        }

        $transaction = Transaction::find($this->selectedTransactionId);

        if (!$transaction || $transaction->customer_id !== Auth::guard('customer')->id()) {
            $this->error(
                title: 'Pesanan Tidak Ditemukan!',
                description: 'Pesanan yang Anda cari tidak tersedia.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        if ($transaction->workflow_status !== 'pending_confirmation') {
            $this->error(
                title: 'Tidak Dapat Dibatalkan!',
                description: 'Hanya pesanan dengan status pending yang bisa dibatalkan.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        $transaction->workflow_status = 'cancelled';
        $transaction->save();

        $this->success(
            title: 'Pesanan Dibatalkan!',
            description: 'Pesanan Anda berhasil dibatalkan.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
        $this->showCancelModal = false;
        $this->selectedTransactionId = null;

        unset($this->transactions);
        unset($this->totalTransactions);
    }

    public function render()
    {
        return view('livewire.pelanggan.pesanan');
    }
}

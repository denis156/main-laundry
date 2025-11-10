<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Mary\Traits\Toast;
use App\Helper\TransactionAreaFilter;
use App\Helper\Database\CourierHelper;
use App\Helper\Database\CustomerHelper;
use App\Helper\Database\TransactionHelper;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

#[Title('Pesanan')]
#[Layout('components.layouts.kurir')]
class Pesanan extends Component
{
    use Toast;

    public string $filter = 'all'; // all, pending_confirmation, confirmed, picked_up, at_loading_post, in_washing, washing_completed, out_for_delivery, delivered, cancelled
    public string $search = '';

    // Pagination manual
    public int $perPage = 5;
    public int $currentPage = 1;

    // Modal states
    public bool $showCancelModal = false;
    public bool $showConfirmModal = false;
    public bool $showAtLoadingPostModal = false;
    public bool $showOutForDeliveryModal = false;
    public bool $showDeliveredModal = false;

    // ID transaksi yang akan diproses
    public ?int $selectedTransactionId = null;

    /**
     * Refresh orders - dipanggil dari JavaScript saat menerima broadcast event
     */
    #[On('refresh-orders')]
    public function refreshOrders(): void
    {
        // Refresh computed properties untuk load data terbaru
        unset($this->transactions);
    }

    /**
     * Get total count transaksi untuk pagination
     */
    #[Computed]
    public function totalTransactions(): int
    {
        $courier = Auth::guard('courier')->user();

        // Load pos dengan area layanan
        $assignedPos = $courier->assignedPos;

        $query = Transaction::with(['customer', 'location'])
            ->where(function ($q) use ($courier) {
                // Transaksi yang sudah di-assign ke kurir ini
                $q->where('courier_id', $courier->id)
                    // ATAU transaksi yang belum ada kurirnya (bisa diambil)
                    ->orWhereNull('courier_id');
            })
            ->whereNotNull('customer_id')
            ->whereHas('customer');

        // Filter berdasarkan area layanan pos menggunakan helper
        TransactionAreaFilter::applyFilter($query, $assignedPos);

        // Filter berdasarkan workflow_status
        if ($this->filter !== 'all') {
            $query->where('workflow_status', $this->filter);
        }

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($subQ) {
                        $subQ->whereRaw("data->>'name' ILIKE ?", ['%' . $this->search . '%']);
                    });
            });
        }

        return $query->count();
    }

    /**
     * Get transaksi yang di-handle oleh kurir yang sedang login
     * atau transaksi yang belum ada kurirnya (untuk bisa diambil)
     * Hanya menampilkan transaksi di area layanan pos kurir
     */
    #[Computed]
    public function transactions(): Collection
    {
        $courier = Auth::guard('courier')->user();

        // Load pos dengan area layanan
        $assignedPos = $courier->assignedPos;

        $query = Transaction::with(['customer', 'location'])
            ->where(function ($q) use ($courier) {
                // Transaksi yang sudah di-assign ke kurir ini
                $q->where('courier_id', $courier->id)
                    // ATAU transaksi yang belum ada kurirnya (bisa diambil)
                    ->orWhereNull('courier_id');
            })
            ->whereNotNull('customer_id')
            ->whereHas('customer');

        // Filter berdasarkan area layanan pos menggunakan helper
        TransactionAreaFilter::applyFilter($query, $assignedPos);

        // Filter berdasarkan workflow_status
        if ($this->filter !== 'all') {
            $query->where('workflow_status', $this->filter);
        }

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
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
        unset($this->transactions);
        unset($this->totalTransactions);
    }

    /**
     * Tampilkan lebih sedikit data
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
     * Check apakah masih ada data lagi
     */
    public function hasMore(): bool
    {
        return ($this->perPage * $this->currentPage) < $this->totalTransactions;
    }

    /**
     * Check apakah bisa load less
     */
    public function canLoadLess(): bool
    {
        return $this->currentPage > 1;
    }


    /**
     * Generate WhatsApp URL dengan message untuk customer (pickup)
     */
    public function getWhatsAppUrl(string $phone, string $customerName, Transaction $transaction): string
    {
        $courier = Auth::guard('courier')->user();

        // Format nomor telepon (hapus karakter non-numeric)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

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

        // Get data from JSONB
        $items = TransactionHelper::getItems($transaction);
        $firstItem = $items[0] ?? [];
        $pricePerKg = number_format((float) ($firstItem['price_per_kg'] ?? 0), 0, ',', '.');
        $serviceName = $firstItem['service_name'] ?? null;

        // Fallback ke Service model jika service_name tidak ada
        if (!$serviceName && !empty($firstItem['service_id'])) {
            $service = \App\Models\Service::find($firstItem['service_id']);
            $serviceName = $service?->name ?? 'Layanan';
        }
        $serviceName = $serviceName ?: 'Layanan';

        $invoiceNumber = $transaction->invoice_number;

        // Get customer address from JSONB
        $defaultAddress = CustomerHelper::getDefaultAddress($transaction->customer);
        $customerAddress = $defaultAddress ? CustomerHelper::getFullAddressString($defaultAddress) : 'Alamat belum tersedia';

        $courierName = CourierHelper::getName($courier);

        // Message template dengan info layanan dan harga
        $message = "Halo Kak *{$customerName}*\n\n";
        $message .= "Perkenalkan, saya *{$courierName}* dari *Main Laundry*. ";
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
    public function getWhatsAppUrlForDelivery(string $phone, string $customerName, Transaction $transaction): string
    {
        $courier = Auth::guard('courier')->user();

        // Format nomor telepon (hapus karakter non-numeric)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

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

        // Get data from JSONB
        $items = TransactionHelper::getItems($transaction);
        $totalPrice = number_format((float) TransactionHelper::getTotalPrice($transaction), 0, ',', '.');
        $firstItem = $items[0] ?? [];
        $pricePerKg = number_format((float) ($firstItem['price_per_kg'] ?? 0), 0, ',', '.');

        // Calculate total weight from items
        $weight = 0;
        foreach ($items as $item) {
            $weight += $item['weight'] ?? $item['total_weight'] ?? 0;
        }

        $serviceName = $firstItem['service_name'] ?? null;

        // Fallback ke Service model jika service_name tidak ada
        if (!$serviceName && !empty($firstItem['service_id'])) {
            $service = \App\Models\Service::find($firstItem['service_id']);
            $serviceName = $service?->name ?? 'Layanan';
        }
        $serviceName = $serviceName ?: 'Layanan';

        $invoiceNumber = $transaction->invoice_number;
        $isPaid = $transaction->payment_status === 'paid';
        $paymentTiming = TransactionHelper::getPaymentTimingText($transaction);

        // Get customer address from JSONB
        $defaultAddress = CustomerHelper::getDefaultAddress($transaction->customer);
        $customerAddress = $defaultAddress ? CustomerHelper::getFullAddressString($defaultAddress) : 'Alamat belum tersedia';

        $courierName = CourierHelper::getName($courier);

        // Message template untuk pengantaran dengan detail lengkap
        $message = "Halo Kak *{$customerName}*\n\n";
        $message .= "Kabar baik! Cucian Kakak sudah selesai dan siap diantar.\n\n";
        $message .= "Saya *{$courierName}* dari *Main Laundry* akan mengantar cucian Kakak hari ini.\n\n";
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
     * Buka modal konfirmasi untuk ambil pesanan
     */
    public function openConfirmModal(int $transactionId): void
    {
        $this->selectedTransactionId = $transactionId;
        $this->showConfirmModal = true;
    }

    /**
     * Konfirmasi dan ambil pesanan (ubah status dari pending_confirmation ke confirmed)
     * Jika belum ada kurir, assign kurir ini ke pesanan tersebut
     * Validasi: pesanan harus di area layanan pos kurir
     */
    public function confirmOrder(): void
    {
        $courier = Auth::guard('courier')->user();
        $assignedPos = $courier->assignedPos;

        $query = Transaction::where('id', $this->selectedTransactionId)
            ->where(function ($q) use ($courier) {
                // Transaksi yang sudah di-assign ke kurir ini ATAU belum ada kurirnya
                $q->where('courier_id', $courier->id)
                    ->orWhereNull('courier_id');
            })
            ->where('workflow_status', 'pending_confirmation');

        // Filter berdasarkan area layanan pos menggunakan helper (security)
        TransactionAreaFilter::applyFilter($query, $assignedPos);

        $transaction = $query->first();

        if (!$transaction) {
            $this->error(
                title: 'Pesanan Tidak Ditemukan!',
                description: 'Pesanan tidak ditemukan, tidak bisa dikonfirmasi, atau di luar area layanan Anda.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        $transaction->update([
            'courier_id' => $courier->id, // Assign kurir ke transaksi
            'workflow_status' => 'confirmed',
        ]);

        $this->success(
            title: 'Pesanan Dikonfirmasi!',
            description: 'Pesanan berhasil diambil dan dikonfirmasi! Silahkan hubungi customer untuk koordinasi pickup.',
            position: 'toast-top toast-end',
            timeout: 3000
        );

        // Close modal dan reset selected transaction
        $this->showConfirmModal = false;
        $this->selectedTransactionId = null;

        // Refresh data
        unset($this->transactions);
    }

    /**
     * Buka modal konfirmasi untuk batalkan pesanan
     */
    public function openCancelModal(int $transactionId): void
    {
        $this->selectedTransactionId = $transactionId;
        $this->showCancelModal = true;
    }

    /**
     * Batalkan pesanan (ubah status dari pending_confirmation ke cancelled)
     * Validasi: pesanan harus di area layanan pos kurir
     * Assign kurir ke transaksi untuk tracking siapa yang membatalkan
     */
    public function cancelOrder(): void
    {
        $courier = Auth::guard('courier')->user();
        $assignedPos = $courier->assignedPos;

        $query = Transaction::where('id', $this->selectedTransactionId)
            ->where(function ($q) use ($courier) {
                // Transaksi yang sudah di-assign ke kurir ini ATAU belum ada kurirnya
                $q->where('courier_id', $courier->id)
                    ->orWhereNull('courier_id');
            })
            ->where('workflow_status', 'pending_confirmation');

        // Filter berdasarkan area layanan pos menggunakan helper (security)
        TransactionAreaFilter::applyFilter($query, $assignedPos);

        $transaction = $query->first();

        if (!$transaction) {
            $this->error(
                title: 'Pesanan Tidak Ditemukan!',
                description: 'Pesanan tidak ditemukan, tidak bisa dibatalkan, atau di luar area layanan Anda.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Assign kurir ke transaksi untuk tracking siapa yang membatalkan
        $transaction->update([
            'courier_id' => $courier->id,
            'workflow_status' => 'cancelled',
        ]);

        $this->success(
            title: 'Pesanan Dibatalkan!',
            description: 'Pesanan berhasil dibatalkan.',
            position: 'toast-top toast-end',
            timeout: 3000
        );

        // Close modal dan reset selected transaction
        $this->showCancelModal = false;
        $this->selectedTransactionId = null;

        // Refresh data
        unset($this->transactions);
    }


    /**
     * Buka modal konfirmasi untuk tandai pesanan sudah di pos
     */
    public function openAtLoadingPostModal(int $transactionId): void
    {
        $this->selectedTransactionId = $transactionId;
        $this->showAtLoadingPostModal = true;
    }

    /**
     * Tandai pesanan sudah di pos (ubah status dari picked_up ke at_loading_post)
     */
    public function markAsAtLoadingPost(): void
    {
        $courier = Auth::guard('courier')->user();

        $transaction = Transaction::where('id', $this->selectedTransactionId)
            ->where('courier_id', $courier->id)
            ->where('workflow_status', 'picked_up')
            ->first();

        if (!$transaction) {
            $this->error(
                title: 'Tidak Bisa Diupdate!',
                description: 'Pesanan tidak ditemukan atau tidak bisa diupdate.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        $transaction->update([
            'workflow_status' => 'at_loading_post',
        ]);

        $this->success(
            title: 'Pesanan Di Pos!',
            description: 'Pesanan berhasil ditandai sudah di pos loading.',
            position: 'toast-top toast-end',
            timeout: 3000
        );

        // Close modal dan reset selected transaction
        $this->showAtLoadingPostModal = false;
        $this->selectedTransactionId = null;

        // Refresh data
        unset($this->transactions);
    }

    /**
     * Buka modal konfirmasi untuk tandai pesanan dalam pengiriman
     */
    public function openOutForDeliveryModal(int $transactionId): void
    {
        $this->selectedTransactionId = $transactionId;
        $this->showOutForDeliveryModal = true;
    }

    /**
     * Tandai pesanan dalam pengiriman (ubah status dari washing_completed ke out_for_delivery)
     */
    public function markAsOutForDelivery(): void
    {
        $courier = Auth::guard('courier')->user();

        $transaction = Transaction::where('id', $this->selectedTransactionId)
            ->where('courier_id', $courier->id)
            ->where('workflow_status', 'washing_completed')
            ->first();

        if (!$transaction) {
            $this->error(
                title: 'Tidak Bisa Diupdate!',
                description: 'Pesanan tidak ditemukan atau tidak bisa diupdate.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        $transaction->update([
            'workflow_status' => 'out_for_delivery',
        ]);

        $this->success(
            title: 'Dalam Pengiriman!',
            description: 'Pesanan berhasil ditandai dalam pengiriman.',
            position: 'toast-top toast-end',
            timeout: 3000
        );

        // Close modal dan reset selected transaction
        $this->showOutForDeliveryModal = false;
        $this->selectedTransactionId = null;

        // Refresh data
        unset($this->transactions);
    }

    /**
     * Buka modal konfirmasi untuk tandai pesanan terkirim
     */
    public function openDeliveredModal(int $transactionId): void
    {
        $this->selectedTransactionId = $transactionId;
        $this->showDeliveredModal = true;
    }

    /**
     * Tandai pesanan terkirim (ubah status dari out_for_delivery ke delivered)
     * Upload bukti pembayaran jika payment_timing adalah on_delivery
     */
    public function markAsDelivered(): void
    {
        $courier = Auth::guard('courier')->user();

        $transaction = Transaction::where('id', $this->selectedTransactionId)
            ->where('courier_id', $courier->id)
            ->where('workflow_status', 'out_for_delivery')
            ->first();

        if (!$transaction) {
            $this->error(
                title: 'Tidak Bisa Diupdate!',
                description: 'Pesanan tidak ditemukan atau tidak bisa diupdate.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        $transaction->update([
            'workflow_status' => 'delivered',
        ]);

        $this->success(
            title: 'Pesanan Terkirim!',
            description: 'Pesanan berhasil ditandai terkirim.',
            position: 'toast-top toast-end',
            timeout: 3000
        );

        // Close modal dan reset selected transaction
        $this->showDeliveredModal = false;
        $this->selectedTransactionId = null;

        // Refresh data
        unset($this->transactions);
    }

    public function render()
    {
        return view('livewire.kurir.pesanan', [
            'hasMore' => $this->hasMore(),
            'canLoadLess' => $this->canLoadLess(),
        ]);
    }
}

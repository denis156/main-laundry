<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Mary\Traits\Toast;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

#[Title('Pesanan')]
#[Layout('components.layouts.mobile')]
class Pesanan extends Component
{
    use Toast, WithFileUploads;

    public string $filter = 'all'; // all, pending_confirmation, confirmed, picked_up, at_loading_post, in_washing, washing_completed, out_for_delivery, delivered, cancelled
    public string $search = '';

    // Array untuk menyimpan berat per transaksi [transaction_id => weight]
    public array $weights = [];

    // Array untuk menyimpan bukti pembayaran per transaksi [transaction_id => file]
    public array $paymentProofs = [];

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

        $query = Transaction::with(['customer', 'service', 'pos'])
            ->where(function ($q) use ($courier) {
                // Transaksi yang sudah di-assign ke kurir ini
                $q->where('courier_motorcycle_id', $courier->id)
                    // ATAU transaksi yang belum ada kurirnya (bisa diambil)
                    ->orWhereNull('courier_motorcycle_id');
            })
            ->whereNotNull('customer_id')
            ->whereNotNull('service_id')
            ->whereHas('customer')
            ->whereHas('service');

        // Filter berdasarkan area layanan pos (hanya tampilkan transaksi yang customernya di area pos)
        if ($assignedPos && !empty($assignedPos->area)) {
            $query->whereHas('customer', function ($q) use ($assignedPos) {
                $q->where(function ($subQ) use ($assignedPos) {
                    // Customer village_name harus ada dalam pos.area (JSON array)
                    foreach ($assignedPos->area as $kelurahan) {
                        $subQ->orWhere('village_name', $kelurahan);
                    }
                    // ATAU customer belum ada village_name (backward compatibility)
                    $subQ->orWhereNull('village_name');
                });
            });
        }

        // Filter berdasarkan workflow_status
        if ($this->filter !== 'all') {
            $query->where('workflow_status', $this->filter);
        }

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return $query->orderBy('order_date', 'desc')
            ->get();
    }

    /**
     * Get statistik pesanan (hanya dari area layanan pos kurir)
     */
    #[Computed]
    public function stats(): array
    {
        $courier = Auth::guard('courier')->user();

        // Load pos dengan area layanan
        $assignedPos = $courier->assignedPos;

        // Base query dengan filter area
        $baseQuery = function () use ($courier, $assignedPos) {
            $query = Transaction::query();

            // Filter berdasarkan area layanan pos
            if ($assignedPos && !empty($assignedPos->area)) {
                $query->whereHas('customer', function ($q) use ($assignedPos) {
                    $q->where(function ($subQ) use ($assignedPos) {
                        foreach ($assignedPos->area as $kelurahan) {
                            $subQ->orWhere('village_name', $kelurahan);
                        }
                        $subQ->orWhereNull('village_name');
                    });
                });
            }

            return $query;
        };

        $pendingCount = $baseQuery()
            ->where(function ($q) use ($courier) {
                $q->where('courier_motorcycle_id', $courier->id)
                    ->orWhereNull('courier_motorcycle_id');
            })
            ->where('workflow_status', 'pending_confirmation')
            ->count();

        $activeCount = $baseQuery()
            ->where('courier_motorcycle_id', $courier->id)
            ->whereIn('workflow_status', ['confirmed', 'picked_up', 'at_loading_post', 'in_washing', 'washing_completed', 'out_for_delivery'])
            ->count();

        $deliveredCount = $baseQuery()
            ->where('courier_motorcycle_id', $courier->id)
            ->where('workflow_status', 'delivered')
            ->count();

        $cancelledCount = $baseQuery()
            ->where('courier_motorcycle_id', $courier->id)
            ->where('workflow_status', 'cancelled')
            ->count();

        return [
            'pending_count' => $pendingCount,
            'active_count' => $activeCount,
            'delivered_count' => $deliveredCount,
            'cancelled_count' => $cancelledCount,
        ];
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

        // Format harga
        $pricePerKg = number_format((float) $transaction->price_per_kg, 0, ',', '.');
        $serviceName = $transaction->service?->name ?? 'Layanan';
        $invoiceNumber = $transaction->invoice_number;
        $customerAddress = $transaction->customer?->address ?? 'Alamat belum tersedia';

        // Message template dengan info layanan dan harga
        $message = "Halo Kak *{$customerName}*\n\n";
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

        // Format harga dan detail
        $totalPrice = number_format((float) $transaction->total_price, 0, ',', '.');
        $weight = $transaction->weight;
        $serviceName = $transaction->service?->name ?? 'Layanan';
        $invoiceNumber = $transaction->invoice_number;
        $isPaid = $transaction->payment_status === 'paid';
        $paymentTiming = $transaction->payment_timing === 'on_delivery' ? 'Bayar Saat Antar' : 'Bayar Saat Jemput';
        $customerAddress = $transaction->customer?->address ?? 'Alamat belum tersedia';

        // Message template untuk pengantaran dengan detail lengkap
        $message = "Halo Kak *{$customerName}*\n\n";
        $message .= "Kabar baik! Cucian Kakak sudah selesai dan siap diantar.\n\n";
        $message .= "Saya *{$courier->name}* dari *Main Laundry* akan mengantar cucian Kakak hari ini.\n\n";
        $message .= "*Detail Pesanan:*\n";
        $message .= "• Invoice: {$invoiceNumber}\n";
        $message .= "• Layanan: {$serviceName}\n";
        $message .= "• Berat: {$weight} kg\n";
        $message .= "• Total: Rp {$totalPrice}\n";
        $message .= "• Pembayaran: {$paymentTiming}\n";

        if ($isPaid) {
            $message .= "• Status: *Sudah Lunas*\n\n";
        } else {
            $message .= "• Status: *Belum Lunas*\n\n";
            $message .= "Mohon siapkan uang cash Rp {$totalPrice} ya Kak.\n\n";
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
     * Jika belum ada kurir, assign kurir ini ke pesanan tersebut
     * Validasi: pesanan harus di area layanan pos kurir
     */
    public function confirmOrder(int $transactionId): void
    {
        $courier = Auth::guard('courier')->user();
        $assignedPos = $courier->assignedPos;

        $query = Transaction::where('id', $transactionId)
            ->where(function ($q) use ($courier) {
                // Transaksi yang sudah di-assign ke kurir ini ATAU belum ada kurirnya
                $q->where('courier_motorcycle_id', $courier->id)
                    ->orWhereNull('courier_motorcycle_id');
            })
            ->where('workflow_status', 'pending_confirmation');

        // Filter berdasarkan area layanan pos (security)
        if ($assignedPos && !empty($assignedPos->area)) {
            $query->whereHas('customer', function ($q) use ($assignedPos) {
                $q->where(function ($subQ) use ($assignedPos) {
                    foreach ($assignedPos->area as $kelurahan) {
                        $subQ->orWhere('village_name', $kelurahan);
                    }
                    $subQ->orWhereNull('village_name');
                });
            });
        }

        $transaction = $query->first();

        if (!$transaction) {
            $this->error('Pesanan tidak ditemukan, tidak bisa dikonfirmasi, atau di luar area layanan Anda.');
            return;
        }

        $transaction->update([
            'courier_motorcycle_id' => $courier->id, // Assign kurir ke transaksi
            'workflow_status' => 'confirmed',
        ]);

        $this->success('Pesanan berhasil diambil dan dikonfirmasi! Silahkan hubungi customer untuk koordinasi pickup.');

        // Refresh data
        unset($this->transactions);
        unset($this->stats);
    }

    /**
     * Batalkan pesanan (ubah status dari pending_confirmation ke cancelled)
     * Validasi: pesanan harus di area layanan pos kurir
     */
    public function cancelOrder(int $transactionId): void
    {
        $courier = Auth::guard('courier')->user();
        $assignedPos = $courier->assignedPos;

        $query = Transaction::where('id', $transactionId)
            ->where(function ($q) use ($courier) {
                // Transaksi yang sudah di-assign ke kurir ini ATAU belum ada kurirnya
                $q->where('courier_motorcycle_id', $courier->id)
                    ->orWhereNull('courier_motorcycle_id');
            })
            ->where('workflow_status', 'pending_confirmation');

        // Filter berdasarkan area layanan pos (security)
        if ($assignedPos && !empty($assignedPos->area)) {
            $query->whereHas('customer', function ($q) use ($assignedPos) {
                $q->where(function ($subQ) use ($assignedPos) {
                    foreach ($assignedPos->area as $kelurahan) {
                        $subQ->orWhere('village_name', $kelurahan);
                    }
                    $subQ->orWhereNull('village_name');
                });
            });
        }

        $transaction = $query->first();

        if (!$transaction) {
            $this->error('Pesanan tidak ditemukan, tidak bisa dibatalkan, atau di luar area layanan Anda.');
            return;
        }

        $transaction->update([
            'workflow_status' => 'cancelled',
        ]);

        $this->success('Pesanan berhasil dibatalkan.');

        // Refresh data
        unset($this->transactions);
        unset($this->stats);
    }

    /**
     * Tandai pesanan sudah dijemput (ubah status dari confirmed ke picked_up)
     * Update pos_id sesuai dengan pos kurir dan simpan berat yang ditimbang
     */
    public function markAsPickedUp(int $transactionId): void
    {
        $courier = Auth::guard('courier')->user();

        // Validasi berat harus diisi
        if (empty($this->weights[$transactionId]) || $this->weights[$transactionId] <= 0) {
            $this->error('Berat cucian harus diisi dan lebih dari 0 kg!');
            return;
        }

        $transaction = Transaction::where('id', $transactionId)
            ->where('courier_motorcycle_id', $courier->id)
            ->where('workflow_status', 'confirmed')
            ->first();

        if (!$transaction) {
            $this->error('Pesanan tidak ditemukan atau tidak bisa diupdate.');
            return;
        }

        // Validasi bukti pembayaran jika bayar saat jemput
        if ($transaction->payment_timing === 'on_pickup') {
            if (empty($this->paymentProofs[$transactionId])) {
                $this->error('Bukti pembayaran harus diupload untuk pesanan yang bayar saat jemput!');
                return;
            }
        }

        $weight = (float) $this->weights[$transactionId];
        $pricePerKg = $transaction->price_per_kg;
        $totalPrice = $weight * $pricePerKg;

        $updateData = [
            'workflow_status' => 'picked_up',
            'pos_id' => $courier->assigned_pos_id,
            'weight' => $weight,
            'total_price' => $totalPrice,
        ];

        // Handle upload bukti pembayaran jika bayar saat jemput
        if ($transaction->payment_timing === 'on_pickup' && !empty($this->paymentProofs[$transactionId])) {
            $file = $this->paymentProofs[$transactionId];
            $filename = 'payment-proof-' . $transaction->invoice_number . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payment-proofs', $filename, 'public');

            $updateData['payment_proof_url'] = $path;
            $updateData['payment_status'] = 'paid';
            $updateData['paid_at'] = now();
        }

        $transaction->update($updateData);

        $message = 'Pesanan berhasil ditandai sudah dijemput dengan berat ' . $weight . ' kg!';
        if ($transaction->payment_timing === 'on_pickup') {
            $message .= ' Pembayaran telah terkonfirmasi.';
        }

        $this->success($message);

        // Clear inputs setelah berhasil
        unset($this->weights[$transactionId]);
        unset($this->paymentProofs[$transactionId]);

        // Refresh data
        unset($this->transactions);
        unset($this->stats);
    }

    /**
     * Get hint text untuk total harga berdasarkan berat yang diinput
     */
    public function getTotalPriceHint(Transaction $transaction): string
    {
        $weight = $this->weights[$transaction->id] ?? 0;

        if ($weight <= 0 || $transaction->price_per_kg <= 0) {
            return 'Masukkan berat untuk melihat total harga';
        }

        $totalPrice = $weight * $transaction->price_per_kg;

        return 'Total: Rp ' . number_format($totalPrice, 0, ',', '.');
    }

    /**
     * Tandai pesanan sudah di pos (ubah status dari picked_up ke at_loading_post)
     */
    public function markAsAtLoadingPost(int $transactionId): void
    {
        $courier = Auth::guard('courier')->user();

        $transaction = Transaction::where('id', $transactionId)
            ->where('courier_motorcycle_id', $courier->id)
            ->where('workflow_status', 'picked_up')
            ->first();

        if (!$transaction) {
            $this->error('Pesanan tidak ditemukan atau tidak bisa diupdate.');
            return;
        }

        $transaction->update([
            'workflow_status' => 'at_loading_post',
        ]);

        $this->success('Pesanan berhasil ditandai sudah di pos loading!');

        // Refresh data
        unset($this->transactions);
        unset($this->stats);
    }

    /**
     * Tandai pesanan dalam pengiriman (ubah status dari washing_completed ke out_for_delivery)
     */
    public function markAsOutForDelivery(int $transactionId): void
    {
        $courier = Auth::guard('courier')->user();

        $transaction = Transaction::where('id', $transactionId)
            ->where('courier_motorcycle_id', $courier->id)
            ->where('workflow_status', 'washing_completed')
            ->first();

        if (!$transaction) {
            $this->error('Pesanan tidak ditemukan atau tidak bisa diupdate.');
            return;
        }

        $transaction->update([
            'workflow_status' => 'out_for_delivery',
        ]);

        $this->success('Pesanan berhasil ditandai dalam pengiriman!');

        // Refresh data
        unset($this->transactions);
        unset($this->stats);
    }

    /**
     * Tandai pesanan terkirim (ubah status dari out_for_delivery ke delivered)
     * Upload bukti pembayaran jika payment_timing adalah on_delivery
     */
    public function markAsDelivered(int $transactionId): void
    {
        $courier = Auth::guard('courier')->user();

        $transaction = Transaction::where('id', $transactionId)
            ->where('courier_motorcycle_id', $courier->id)
            ->where('workflow_status', 'out_for_delivery')
            ->first();

        if (!$transaction) {
            $this->error('Pesanan tidak ditemukan atau tidak bisa diupdate.');
            return;
        }

        // Validasi bukti pembayaran jika bayar saat antar
        if ($transaction->payment_timing === 'on_delivery') {
            if (empty($this->paymentProofs[$transactionId])) {
                $this->error('Bukti pembayaran harus diupload untuk pesanan yang bayar saat antar!');
                return;
            }
        }

        $updateData = [
            'workflow_status' => 'delivered',
        ];

        // Handle upload bukti pembayaran jika bayar saat antar
        if ($transaction->payment_timing === 'on_delivery' && !empty($this->paymentProofs[$transactionId])) {
            $file = $this->paymentProofs[$transactionId];
            $filename = 'payment-proof-' . $transaction->invoice_number . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payment-proofs', $filename, 'public');

            $updateData['payment_proof_url'] = $path;
            $updateData['payment_status'] = 'paid';
            $updateData['paid_at'] = now();
        }

        $transaction->update($updateData);

        $message = 'Pesanan berhasil ditandai terkirim!';
        if ($transaction->payment_timing === 'on_delivery') {
            $message .= ' Pembayaran telah terkonfirmasi.';
        }

        $this->success($message);

        // Clear inputs setelah berhasil
        unset($this->paymentProofs[$transactionId]);

        // Refresh data
        unset($this->transactions);
        unset($this->stats);
    }

    public function render()
    {
        return view('livewire.kurir.pesanan');
    }
}

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
use Illuminate\Support\Facades\Auth;

#[Title('Detail Pesanan')]
#[Layout('components.layouts.kurir')]
class DetailPesanan extends Component
{
    use Toast;

    public Transaction $transaction;

    // Input data untuk setiap item/layanan (array)
    public array $itemInputs = []; // Format: ['service_id' => ['weight' => 0, 'quantity' => 0, 'clothing_items' => []]]

    // Available clothing types untuk dropdown
    public $clothingTypes = [];

    // Modal states
    public bool $showCancelModal = false;
    public bool $showConfirmModal = false;
    public bool $showPickedUpModal = false;
    public bool $showAtLoadingPostModal = false;
    public bool $showOutForDeliveryModal = false;
    public bool $showDeliveredModal = false;

    public function mount(int $id): void
    {
        $courier = Auth::guard('courier')->user();

        // Load transaction dengan semua relasi yang diperlukan
        $this->transaction = Transaction::with([
            'customer',
            'location',
            'courier',
            'payments'
        ])
            ->where('id', $id)
            ->where(function ($q) use ($courier) {
                // Hanya transaksi yang di-handle oleh kurir ini atau belum ada kurirnya
                $q->where('courier_id', $courier->id)
                    ->orWhereNull('courier_id');
            })
            ->firstOrFail();

        // Load active clothing types untuk dropdown
        $this->clothingTypes = \App\Models\ClothingType::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Initialize itemInputs dari transaction data
        $items = TransactionHelper::getItems($this->transaction);
        foreach ($items as $index => $item) {
            $serviceId = $item['service_id'] ?? $index;
            $pricingUnit = $item['pricing_unit'] ?? 'per_kg';
            $clothingItems = $item['clothing_items'] ?? [];

            // Untuk per_kg, jika clothing_items kosong, auto-add 1 item
            if ($pricingUnit === 'per_kg' && empty($clothingItems)) {
                $clothingItems = [
                    [
                        'clothing_type_id' => null,
                        'clothing_type_name' => '',
                        'quantity' => 1,
                    ]
                ];
            }

            $this->itemInputs[$serviceId] = [
                'total_weight' => $item['total_weight'] ?? 0,
                'quantity' => $item['quantity'] ?? 0,
                'clothing_items' => $clothingItems,
            ];
        }
    }

    /**
     * Generate WhatsApp URL dengan message untuk customer (pickup)
     */
    public function getWhatsAppUrl(): ?string
    {
        $customer = $this->transaction->customer;
        if (!$customer || !$customer->phone || !CustomerHelper::getName($customer)) {
            return null;
        }

        $courier = Auth::guard('courier')->user();

        // Format nomor telepon (hapus karakter non-numeric)
        $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone);

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
        $items = TransactionHelper::getItems($this->transaction);
        $firstItem = $items[0] ?? [];
        $pricePerKg = number_format((float) ($firstItem['price_per_kg'] ?? 0), 0, ',', '.');
        $serviceName = $firstItem['service_name'] ?? 'Layanan';
        $invoiceNumber = $this->transaction->invoice_number;
        $customerAddress = $customer->address ?? 'Alamat belum tersedia';
        $customerName = CustomerHelper::getName($customer);
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
    public function getWhatsAppUrlForDelivery(): ?string
    {
        $customer = $this->transaction->customer;
        if (!$customer || !$customer->phone || !CustomerHelper::getName($customer)) {
            return null;
        }

        $courier = Auth::guard('courier')->user();

        // Format nomor telepon (hapus karakter non-numeric)
        $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone);

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
        $items = TransactionHelper::getItems($this->transaction);
        $totalPrice = number_format((float) TransactionHelper::getTotalPrice($this->transaction), 0, ',', '.');
        $firstItem = $items[0] ?? [];
        $pricePerKg = number_format((float) ($firstItem['price_per_kg'] ?? 0), 0, ',', '.');
        $weight = $this->transaction->weight ?? 0;
        $serviceName = $firstItem['service_name'] ?? 'Layanan';
        $invoiceNumber = $this->transaction->invoice_number;
        $isPaid = $this->transaction->payment_status === 'paid';
        $paymentTiming = TransactionHelper::getPaymentTimingText($this->transaction);
        $customerAddress = $customer->address ?? 'Alamat belum tersedia';
        $customerName = CustomerHelper::getName($customer);
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
    public function openConfirmModal(): void
    {
        $this->showConfirmModal = true;
    }

    /**
     * Konfirmasi dan ambil pesanan (ubah status dari pending_confirmation ke confirmed)
     */
    public function confirmOrder(): void
    {
        $courier = Auth::guard('courier')->user();
        $assignedPos = $courier->assignedPos;

        if ($this->transaction->workflow_status !== 'pending_confirmation') {
            $this->error(
                title: 'Tidak Bisa Dikonfirmasi!',
                description: 'Pesanan tidak bisa dikonfirmasi dengan status saat ini.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Validasi area layanan menggunakan helper
        $isInArea = TransactionAreaFilter::isCustomerInLocationArea($this->transaction->customer, $assignedPos);

        if (!$isInArea) {
            $this->error(
                title: 'Di Luar Area Layanan!',
                description: 'Pesanan ini berada di luar area layanan Anda.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        $this->transaction->update([
            'courier_id' => $courier->id,
            'workflow_status' => 'confirmed',
        ]);

        $this->success(
            title: 'Pesanan Dikonfirmasi!',
            description: 'Pesanan berhasil diambil dan dikonfirmasi.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
        $this->showConfirmModal = false;
        $this->transaction->refresh();
    }

    /**
     * Buka modal konfirmasi untuk batalkan pesanan
     */
    public function openCancelModal(): void
    {
        $this->showCancelModal = true;
    }

    /**
     * Batalkan pesanan (ubah status dari pending_confirmation ke cancelled)
     * Assign kurir ke transaksi untuk tracking siapa yang membatalkan
     */
    public function cancelOrder(): void
    {
        $courier = Auth::guard('courier')->user();

        if ($this->transaction->workflow_status !== 'pending_confirmation') {
            $this->error(
                title: 'Tidak Bisa Dibatalkan!',
                description: 'Pesanan tidak bisa dibatalkan dengan status saat ini.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Assign kurir ke transaksi untuk tracking siapa yang membatalkan
        $this->transaction->update([
            'courier_id' => $courier->id,
            'workflow_status' => 'cancelled',
        ]);

        $this->success(
            title: 'Pesanan Dibatalkan!',
            description: 'Pesanan berhasil dibatalkan.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
        $this->showCancelModal = false;
        $this->transaction->refresh();
    }

    /**
     * Check apakah button "Sudah Dijemput" disabled
     */
    public function isPickedUpButtonDisabled(): bool
    {
        $items = TransactionHelper::getItems($this->transaction);

        foreach ($items as $index => $item) {
            $serviceId = $item['service_id'] ?? $index;
            $pricingUnit = $item['pricing_unit'] ?? 'per_kg';
            $input = $this->itemInputs[$serviceId] ?? [];

            if ($pricingUnit === 'per_kg') {
                // Check weight dan clothing items
                $weight = $input['total_weight'] ?? 0;
                $clothingItems = $input['clothing_items'] ?? [];

                if ($weight <= 0) {
                    return true; // Disabled jika berat belum diisi
                }

                if (empty($clothingItems)) {
                    return true; // Disabled jika clothing items kosong
                }

                // Check apakah ada clothing item yang belum lengkap
                foreach ($clothingItems as $clothing) {
                    if (empty($clothing['clothing_type_id']) || ($clothing['quantity'] ?? 0) <= 0) {
                        return true; // Disabled jika ada yang belum lengkap
                    }
                }
            } else {
                // Check quantity untuk per_item
                $quantity = $input['quantity'] ?? 0;
                if ($quantity <= 0) {
                    return true; // Disabled jika quantity belum diisi
                }
            }
        }

        return false; // Enable jika semua sudah terisi
    }

    /**
     * Buka modal konfirmasi untuk tandai pesanan dijemput
     */
    public function openPickedUpModal(): void
    {
        $this->showPickedUpModal = true;
    }

    /**
     * Tandai pesanan sudah dijemput (ubah status dari confirmed ke picked_up)
     */
    public function markAsPickedUp(): void
    {
        $courier = Auth::guard('courier')->user();

        if ($this->transaction->workflow_status !== 'confirmed') {
            $this->error(
                title: 'Tidak Bisa Diupdate!',
                description: 'Pesanan tidak bisa diupdate dengan status saat ini.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Validasi semua item harus diisi
        $data = $this->transaction->data ?? [];
        $items = $data['items'] ?? [];

        if (empty($items)) {
            $this->error(
                title: 'Tidak Ada Item!',
                description: 'Tidak ada item untuk diproses.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Update items dengan input data dari kurir
        $totalPrice = 0;
        $hasError = false;
        $errorMessage = '';

        foreach ($items as $index => $item) {
            $serviceId = $item['service_id'] ?? $index;
            $pricingUnit = $item['pricing_unit'] ?? 'per_kg';
            $input = $this->itemInputs[$serviceId] ?? [];

            if ($pricingUnit === 'per_kg') {
                // Validasi weight dan clothing items untuk per_kg
                $weight = $input['total_weight'] ?? 0;
                $clothingItems = $input['clothing_items'] ?? [];

                if ($weight <= 0) {
                    $hasError = true;
                    $errorMessage = 'Berat untuk layanan ' . ($item['service_name'] ?? 'N/A') . ' harus diisi!';
                    break;
                }

                if (empty($clothingItems)) {
                    $hasError = true;
                    $errorMessage = 'Detail pakaian untuk layanan ' . ($item['service_name'] ?? 'N/A') . ' harus diisi!';
                    break;
                }

                $pricePerKg = $item['price_per_kg'] ?? 0;
                $items[$index]['total_weight'] = $weight;
                $items[$index]['clothing_items'] = $clothingItems;
                $items[$index]['subtotal'] = $weight * $pricePerKg;
                $totalPrice += $items[$index]['subtotal'];
            } else {
                // Validasi quantity untuk per_item (tidak perlu clothing items)
                $quantity = $input['quantity'] ?? 0;

                if ($quantity <= 0) {
                    $hasError = true;
                    $errorMessage = 'Jumlah item untuk layanan ' . ($item['service_name'] ?? 'N/A') . ' harus diisi!';
                    break;
                }

                $pricePerItem = $item['price_per_item'] ?? 0;
                $items[$index]['quantity'] = $quantity;
                $items[$index]['subtotal'] = $quantity * $pricePerItem;
                $totalPrice += $items[$index]['subtotal'];
            }
        }

        if ($hasError) {
            $this->error(
                title: 'Validasi Gagal!',
                description: $errorMessage,
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        // Update transaction data
        $data['items'] = $items;

        $this->transaction->update([
            'workflow_status' => 'picked_up',
            'location_id' => $courier->assigned_location_id,
            'data' => $data,
        ]);

        $this->success(
            title: 'Pesanan Dijemput!',
            description: 'Pesanan berhasil ditandai sudah dijemput.',
            position: 'toast-top toast-end',
            timeout: 3000
        );

        $this->showPickedUpModal = false;
        $this->transaction->refresh();
    }

    /**
     * Add clothing item untuk service tertentu
     */
    public function addClothingItem(int $serviceId): void
    {
        if (!isset($this->itemInputs[$serviceId]['clothing_items'])) {
            $this->itemInputs[$serviceId]['clothing_items'] = [];
        }

        $this->itemInputs[$serviceId]['clothing_items'][] = [
            'clothing_type_id' => null,
            'clothing_type_name' => '',
            'quantity' => 1,
        ];
    }

    /**
     * Remove clothing item
     */
    public function removeClothingItem(int $serviceId, int $clothingIndex): void
    {
        if (isset($this->itemInputs[$serviceId]['clothing_items'][$clothingIndex])) {
            unset($this->itemInputs[$serviceId]['clothing_items'][$clothingIndex]);
            // Re-index array
            $this->itemInputs[$serviceId]['clothing_items'] = array_values($this->itemInputs[$serviceId]['clothing_items']);
        }
    }

    /**
     * Update clothing type name when selecting from dropdown
     */
    public function updatedItemInputs($value, $key): void
    {
        // Check if this is a clothing_type_id update
        // Key format: serviceId.clothing_items.index.clothing_type_id
        if (str_contains($key, 'clothing_items') && str_ends_with($key, 'clothing_type_id')) {
            $parts = explode('.', $key);
            if (count($parts) === 4) {
                $serviceId = (int) $parts[0];
                $clothingIndex = (int) $parts[2];
                $clothingTypeId = $value;

                if ($clothingTypeId) {
                    $clothingType = $this->clothingTypes->firstWhere('id', $clothingTypeId);
                    if ($clothingType) {
                        $this->itemInputs[$serviceId]['clothing_items'][$clothingIndex]['clothing_type_name'] = $clothingType->name;
                    }
                }
            }
        }
    }

    /**
     * Get available clothing types untuk service tertentu
     * Filter out yang sudah dipilih di clothing items lain
     */
    public function getAvailableClothingTypes(int $serviceId, int $currentIndex)
    {
        $clothingItems = $this->itemInputs[$serviceId]['clothing_items'] ?? [];

        // Ambil ID yang sudah dipilih (kecuali index saat ini)
        $selectedIds = [];
        foreach ($clothingItems as $index => $item) {
            if ($index !== $currentIndex && !empty($item['clothing_type_id'])) {
                $selectedIds[] = $item['clothing_type_id'];
            }
        }

        // Filter clothing types yang belum dipilih
        return $this->clothingTypes->filter(function ($type) use ($selectedIds) {
            return !in_array($type->id, $selectedIds);
        });
    }

    /**
     * Buka modal konfirmasi untuk tandai pesanan sudah di pos
     */
    public function openAtLoadingPostModal(): void
    {
        $this->showAtLoadingPostModal = true;
    }

    /**
     * Tandai pesanan sudah di pos (ubah status dari picked_up ke at_loading_post)
     */
    public function markAsAtLoadingPost(): void
    {
        if ($this->transaction->workflow_status !== 'picked_up') {
            $this->error(
                title: 'Tidak Bisa Diupdate!',
                description: 'Pesanan tidak bisa diupdate dengan status saat ini.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        $this->transaction->update([
            'workflow_status' => 'at_loading_post',
        ]);

        $this->success(
            title: 'Pesanan Di Pos!',
            description: 'Pesanan berhasil ditandai sudah di pos loading.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
        $this->showAtLoadingPostModal = false;
        $this->transaction->refresh();
    }

    /**
     * Buka modal konfirmasi untuk tandai pesanan dalam pengiriman
     */
    public function openOutForDeliveryModal(): void
    {
        $this->showOutForDeliveryModal = true;
    }

    /**
     * Tandai pesanan dalam pengiriman (ubah status dari washing_completed ke out_for_delivery)
     */
    public function markAsOutForDelivery(): void
    {
        if ($this->transaction->workflow_status !== 'washing_completed') {
            $this->error(
                title: 'Tidak Bisa Diupdate!',
                description: 'Pesanan tidak bisa diupdate dengan status saat ini.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        $this->transaction->update([
            'workflow_status' => 'out_for_delivery',
        ]);

        $this->success(
            title: 'Dalam Pengiriman!',
            description: 'Pesanan berhasil ditandai dalam pengiriman.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
        $this->showOutForDeliveryModal = false;
        $this->transaction->refresh();
    }

    /**
     * Buka modal konfirmasi untuk tandai pesanan terkirim
     */
    public function openDeliveredModal(): void
    {
        $this->showDeliveredModal = true;
    }

    /**
     * Tandai pesanan terkirim (ubah status dari out_for_delivery ke delivered)
     */
    public function markAsDelivered(): void
    {
        if ($this->transaction->workflow_status !== 'out_for_delivery') {
            $this->error(
                title: 'Tidak Bisa Diupdate!',
                description: 'Pesanan tidak bisa diupdate dengan status saat ini.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
            return;
        }

        $this->transaction->update([
            'workflow_status' => 'delivered',
        ]);

        $this->success(
            title: 'Pesanan Terkirim!',
            description: 'Pesanan berhasil ditandai terkirim.',
            position: 'toast-top toast-end',
            timeout: 3000
        );

        $this->showDeliveredModal = false;
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
            'payments'
        ]);

        return view('livewire.kurir.detail-pesanan');
    }
}

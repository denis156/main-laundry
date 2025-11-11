<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Mary\Traits\Toast;
use App\Models\Payment;
use Livewire\Component;
use App\Helper\QrisHelper;
use App\Models\Transaction;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    // QR Code properties
    public bool $showQrModal = false;
    public string $qrCodeUrl = '';
    public string $qrCodeData = '';
    public float $qrAmount = 0;

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
     * Generate QR Code untuk pembayaran dengan nominal
     */
    public function generateQrCode(): void
    {
        try {
            $amount = $this->transaction->data['pricing']['total_price'] ?? 0;

            if ($amount <= 0) {
                $this->error(
                    title: 'Nominal Tidak Valid!',
                    description: 'Nominal pembayaran harus lebih dari 0.',
                    position: 'toast-top toast-end',
                    timeout: 3000
                );
                return;
            }

            // Generate QR Code dengan nominal
            $qrData = QrisHelper::generatePaymentQrCode($amount, $this->transaction->id);

            $this->qrCodeUrl = $qrData['image_url'];
            $this->qrCodeData = $qrData['qris_data'];
            $this->qrAmount = $qrData['amount'];
            $this->showQrModal = true;

            $this->success(
                title: 'QR Code Berhasil Dibuat!',
                description: 'QR Code untuk pembayaran sebesar ' . QrisHelper::formatAmount($amount),
                position: 'toast-top toast-end',
                timeout: 2000
            );
        } catch (\Exception $e) {
            $this->error(
                title: 'Gagal Generate QR Code!',
                description: 'Terjadi kesalahan saat generate QR Code. Silakan coba lagi.',
                position: 'toast-top toast-end',
                timeout: 3000
            );

            Log::error('QR Code Generation Error: ' . $e->getMessage());
        }
    }

    /**
     * Tutup QR Code modal
     */
    public function closeQrModal(): void
    {
        $this->showQrModal = false;
        $this->qrCodeUrl = '';
        $this->qrCodeData = '';
        $this->qrAmount = 0;
    }

    /**
     * Download QR Code image
     */
    public function downloadQrCode()
    {
        if (!empty($this->qrCodeUrl)) {
            // Create download response
            $filename = 'qris-payment-' . $this->transaction->invoice_number . '.png';

            // Extract the relative path from URL
            // Handle different URL formats
            if (str_contains($this->qrCodeUrl, '/storage/')) {
                // Format: http://domain/storage/qrcodes/filename.png
                $relativePath = substr($this->qrCodeUrl, strpos($this->qrCodeUrl, '/storage/') + 9);
            } else {
                // Fallback: assume the format is already relative
                $relativePath = ltrim($this->qrCodeUrl, '/');
            }

            // Ensure relative path starts with qrcodes/
            if (!str_starts_with($relativePath, 'qrcodes/')) {
                $relativePath = 'qrcodes/' . basename($relativePath);
            }

            // Use Storage facade to get the file and return download response
            $storagePath = 'public/' . $relativePath;

            if (Storage::exists($relativePath)) {
                return response()->download(storage_path($storagePath), $filename);
            }
        }

        return null;
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

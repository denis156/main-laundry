<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     * Update payment_status di Transaction menjadi 'paid' HANYA JIKA ada bukti pembayaran
     */
    public function created(Payment $payment): void
    {
        // Cek apakah payment ini punya bukti pembayaran (proof_url tidak null dan tidak empty)
        $proofUrl = $payment->data['proof_url'] ?? null;

        Log::info("PaymentObserver: Payment {$payment->id} created. Proof URL: " . ($proofUrl ?? 'null'));

        // Update status jadi paid HANYA jika ada bukti pembayaran
        if (!empty($proofUrl)) {
            $this->updateTransactionPaymentStatus($payment->transaction_id, 'paid');
        }
    }

    /**
     * Handle the Payment "updated" event.
     * Update payment_status berdasarkan keberadaan bukti pembayaran
     */
    public function updated(Payment $payment): void
    {
        // Cek apakah proof_url berubah (tambah atau dihapus)
        $proofUrl = $payment->data['proof_url'] ?? null;

        Log::info("PaymentObserver: Payment {$payment->id} updated. Proof URL: " . ($proofUrl ?? 'null'));

        // Jika ada bukti pembayaran, status jadi paid
        if (!empty($proofUrl)) {
            $this->updateTransactionPaymentStatus($payment->transaction_id, 'paid');
        } else {
            // Jika tidak ada bukti, status jadi unpaid
            $this->updateTransactionPaymentStatus($payment->transaction_id, 'unpaid');
        }
    }

    /**
     * Handle the Payment "deleted" event.
     * Update payment_status di Transaction menjadi 'unpaid' jika tidak ada payment lain
     */
    public function deleted(Payment $payment): void
    {
        Log::info("PaymentObserver: Payment {$payment->id} deleted");

        // Check apakah transaction masih ada payment lain yang aktif
        $hasOtherPayments = Payment::where('transaction_id', $payment->transaction_id)
            ->where('id', '!=', $payment->id)
            ->exists();

        // Jika tidak ada payment lain, set status jadi unpaid
        if (!$hasOtherPayments) {
            $this->updateTransactionPaymentStatus($payment->transaction_id, 'unpaid');
        }
    }

    /**
     * Handle the Payment "restored" event.
     * Update payment_status berdasarkan keberadaan bukti pembayaran
     */
    public function restored(Payment $payment): void
    {
        Log::info("PaymentObserver: Payment {$payment->id} restored");

        $proofUrl = $payment->data['proof_url'] ?? null;

        // Update status berdasarkan bukti pembayaran
        if (!empty($proofUrl)) {
            $this->updateTransactionPaymentStatus($payment->transaction_id, 'paid');
        } else {
            $this->updateTransactionPaymentStatus($payment->transaction_id, 'unpaid');
        }
    }

    /**
     * Handle the Payment "forceDeleted" event.
     * Update payment_status di Transaction menjadi 'unpaid' jika tidak ada payment lain
     */
    public function forceDeleted(Payment $payment): void
    {
        Log::info("PaymentObserver: Payment {$payment->id} force deleted");

        // Check apakah transaction masih ada payment lain yang aktif
        $hasOtherPayments = Payment::where('transaction_id', $payment->transaction_id)
            ->where('id', '!=', $payment->id)
            ->exists();

        // Jika tidak ada payment lain, set status jadi unpaid
        if (!$hasOtherPayments) {
            $this->updateTransactionPaymentStatus($payment->transaction_id, 'unpaid');
        }
    }

    /**
     * Update payment_status di Transaction
     */
    private function updateTransactionPaymentStatus(int $transactionId, string $status): void
    {
        try {
            $transaction = Transaction::find($transactionId);

            if ($transaction) {
                $oldStatus = $transaction->payment_status;
                $transaction->payment_status = $status;
                $transaction->saveQuietly(); // Save tanpa trigger observer

                Log::info("Payment status updated for transaction {$transactionId}: {$oldStatus} → {$status}");
            } else {
                Log::warning("Transaction {$transactionId} not found when updating payment status");
            }
        } catch (\Exception $e) {
            Log::error("Failed to update payment status for transaction {$transactionId}: " . $e->getMessage());
        }
    }
}
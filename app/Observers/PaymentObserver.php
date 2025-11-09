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
     * Auto-update payment_status di Transaction menjadi 'paid'
     */
    public function created(Payment $payment): void
    {
        $this->updateTransactionPaymentStatus($payment->transaction_id, 'paid');
    }

    /**
     * Handle the Payment "deleted" event.
     * Auto-update payment_status di Transaction menjadi 'unpaid'
     */
    public function deleted(Payment $payment): void
    {
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
     * Auto-update payment_status di Transaction menjadi 'paid'
     */
    public function restored(Payment $payment): void
    {
        $this->updateTransactionPaymentStatus($payment->transaction_id, 'paid');
    }

    /**
     * Handle the Payment "forceDeleted" event.
     * Auto-update payment_status di Transaction menjadi 'unpaid'
     */
    public function forceDeleted(Payment $payment): void
    {
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
                $transaction->payment_status = $status;
                $transaction->saveQuietly(); // Save tanpa trigger observer
            }
        } catch (\Exception $e) {
            Log::error("Failed to update payment status for transaction {$transactionId}: " . $e->getMessage());
        }
    }
}

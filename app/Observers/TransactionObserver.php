<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Str;

class TransactionObserver
{
    /**
     * Handle the Transaction "creating" event.
     * Auto-generate tracking token and capture security data.
     */
    public function creating(Transaction $transaction): void
    {
        // Auto-generate tracking token jika belum ada
        if (empty($transaction->tracking_token)) {
            $transaction->tracking_token = Str::uuid()->toString();
        }

        // Auto-capture customer IP jika belum ada
        if (empty($transaction->customer_ip)) {
            $transaction->customer_ip = request()->ip();
        }

        // Auto-capture user agent jika belum ada
        if (empty($transaction->customer_user_agent)) {
            $transaction->customer_user_agent = request()->userAgent();
        }

        // Note: form_loaded_at akan di-set dari form order di landing page
        // untuk mendeteksi bot submission yang terlalu cepat
    }

    /**
     * Handle the Transaction "updating" event.
     * Auto-calculate total_price sebelum save.
     */
    public function updating(Transaction $transaction): void
    {
        // Auto-calculate total_price ketika weight di-update
        if ($transaction->isDirty('weight') && $transaction->weight > 0 && $transaction->price_per_kg > 0) {
            $transaction->total_price = $transaction->weight * $transaction->price_per_kg;
        }
    }

    /**
     * Handle the Transaction "updated" event.
     * Auto-create Payment record ketika pembayaran terkonfirmasi.
     */
    public function updated(Transaction $transaction): void
    {
        // Cek apakah payment_status berubah menjadi 'paid'
        if ($transaction->wasChanged('payment_status') && $transaction->payment_status === 'paid') {
            // Validasi data lengkap untuk create payment
            if (
                !empty($transaction->payment_proof_url) &&
                !empty($transaction->paid_at) &&
                !empty($transaction->courier_motorcycle_id) &&
                $transaction->total_price > 0
            ) {
                // Cek apakah sudah ada payment record untuk transaksi ini
                $existingPayment = Payment::where('transaction_id', $transaction->id)->first();

                if (!$existingPayment) {
                    // Create payment record
                    Payment::create([
                        'transaction_id' => $transaction->id,
                        'courier_motorcycle_id' => $transaction->courier_motorcycle_id,
                        'amount' => $transaction->total_price,
                        'payment_proof_url' => $transaction->payment_proof_url,
                        'payment_date' => $transaction->paid_at,
                        'notes' => 'Pembayaran ' . ($transaction->payment_timing === 'on_pickup' ? 'saat jemput' : 'saat antar'),
                    ]);
                }
            }
        }
    }
}

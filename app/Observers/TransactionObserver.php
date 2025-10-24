<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\TransactionEvents;
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
     * Handle the Transaction "created" event.
     * Broadcast event untuk real-time notifications.
     */
    public function created(Transaction $transaction): void
    {
        // Broadcast event dengan action 'created'
        event(new TransactionEvents($transaction->load(['customer', 'service']), 'created'));
    }

    /**
     * Handle the Transaction "updated" event.
     * Auto-create Payment record berdasarkan workflow_status dan payment_timing.
     * Broadcast event untuk real-time notifications.
     */
    public function updated(Transaction $transaction): void
    {
        // Auto-create Payment ketika workflow_status berubah ke status tertentu
        if ($transaction->wasChanged('workflow_status')) {
            // Bayar Saat Jemput: Payment dibuat saat status 'picked_up' + weight sudah diinput
            // Payment_status tetap unpaid sampai kurir upload bukti pembayaran
            if (
                $transaction->payment_timing === 'on_pickup' &&
                $transaction->workflow_status === 'picked_up' &&
                !empty($transaction->courier_motorcycle_id) &&
                $transaction->weight > 0 &&
                $transaction->total_price > 0
            ) {
                // Cek apakah payment sudah ada
                $existingPayment = Payment::where('transaction_id', $transaction->id)->first();

                if (!$existingPayment) {
                    Payment::create([
                        'transaction_id' => $transaction->id,
                        'courier_motorcycle_id' => $transaction->courier_motorcycle_id,
                        'amount' => $transaction->total_price,
                        'payment_date' => now(),
                        'notes' => 'Pembayaran saat jemput - Auto-generated',
                    ]);

                    // Payment_status tetap unpaid, akan di-update saat upload bukti pembayaran
                }
            }

            // Bayar Saat Antar: Payment dibuat saat status 'out_for_delivery' (mengantar)
            // Payment_status tetap unpaid sampai kurir upload bukti pembayaran
            if (
                $transaction->payment_timing === 'on_delivery' &&
                $transaction->workflow_status === 'out_for_delivery' &&
                !empty($transaction->courier_motorcycle_id) &&
                $transaction->total_price > 0
            ) {
                // Cek apakah payment sudah ada
                $existingPayment = Payment::where('transaction_id', $transaction->id)->first();

                if (!$existingPayment) {
                    Payment::create([
                        'transaction_id' => $transaction->id,
                        'courier_motorcycle_id' => $transaction->courier_motorcycle_id,
                        'amount' => $transaction->total_price,
                        'payment_date' => now(),
                        'notes' => 'Pembayaran saat antar - Auto-generated',
                    ]);

                    // Payment_status tetap unpaid, akan di-update saat upload bukti pembayaran
                }
            }
        }

        // Broadcast event dengan action 'updated'
        event(new TransactionEvents($transaction->load(['customer', 'service']), 'updated'));
    }

    /**
     * Handle the Transaction "deleted" event.
     * Broadcast event untuk real-time notifications.
     */
    public function deleted(Transaction $transaction): void
    {
        // Broadcast event dengan action 'deleted'
        event(new TransactionEvents($transaction->load(['customer', 'service']), 'deleted'));
    }
}

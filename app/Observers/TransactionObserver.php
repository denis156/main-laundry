<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\TransactionEvents;
use App\Helper\Database\TransactionHelper;
use App\Models\Courier;
use App\Models\Payment;
use App\Models\Transaction;
use App\Notifications\NewTransactionNotification;
use App\Notifications\OrderConfirmedNotification;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionObserver
{
    /**
     * Handle the Transaction "creating" event.
     * Auto-generate tracking token and capture anti-bot data in JSONB.
     * Auto-calculate total_price dari items.
     */
    public function creating(Transaction $transaction): void
    {
        $data = $transaction->data ?? [];

        // Auto-generate tracking token jika belum ada
        if (empty($data['tracking']['tracking_token'])) {
            $data['tracking']['tracking_token'] = Str::uuid()->toString();
        }

        // Auto-capture anti-bot data
        if (empty($data['anti_bot'])) {
            $data['anti_bot'] = [
                'customer_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'form_loaded_at' => $data['anti_bot']['form_loaded_at'] ?? null,
            ];
        }

        // Auto-calculate total_price dari items
        $items = $data['items'] ?? [];
        if (!empty($items)) {
            $totalPrice = 0;
            foreach ($items as $item) {
                $totalPrice += (float) ($item['subtotal'] ?? 0);
            }
            $data['pricing']['total_price'] = $totalPrice;
        }

        $transaction->data = $data;
    }

    /**
     * Handle the Transaction "updating" event.
     * Auto-calculate total_price di JSONB sebelum save.
     */
    public function updating(Transaction $transaction): void
    {
        // Total price calculation sekarang di TransactionHelper
        // Observer hanya ensure consistency
        $data = $transaction->data ?? [];

        // Auto-calculate total jika ada items
        $items = $data['items'] ?? [];
        if (!empty($items)) {
            $totalPrice = 0;
            foreach ($items as $item) {
                $totalPrice += ($item['subtotal'] ?? 0);
            }

            $data['pricing']['total_price'] = $totalPrice;
            $transaction->data = $data;
        }
    }

    /**
     * Handle the Transaction "created" event.
     * Broadcast event untuk real-time notifications.
     * Send web push notification ke semua kurir aktif.
     */
    public function created(Transaction $transaction): void
    {
        // Broadcast event dengan action 'created'
        // Load only existing relations: customer, courier, location
        event(new TransactionEvents($transaction->load(['customer', 'courier', 'location']), 'created'));

        // Send web push notification ke semua kurir aktif yang punya subscription
        // Hanya untuk pesanan baru dengan status pending_confirmation
        if ($transaction->workflow_status === 'pending_confirmation') {
            $this->sendWebPushToActiveCouriers($transaction);
        }
    }

    /**
     * Send web push notification ke semua kurir aktif
     */
    private function sendWebPushToActiveCouriers(Transaction $transaction): void
    {
        // Get semua kurir yang aktif dan punya push subscriptions
        $couriers = Courier::whereNotNull('data')
            ->whereHas('pushSubscriptions')
            ->get()
            ->filter(function ($courier) {
                return ($courier->data['is_active'] ?? true) === true;
            });

        // Send notification ke setiap kurir
        foreach ($couriers as $courier) {
            try {
                $courier->notify(new NewTransactionNotification($transaction));
            } catch (Exception $e) {
                Log::error("Failed to send web push to courier {$courier->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Send web push notification ke customer ketika order dikonfirmasi
     */
    private function sendWebPushToCustomer(Transaction $transaction): void
    {
        // Get customer dan pastikan punya push subscription
        $customer = $transaction->customer;

        if (!$customer || !$customer->pushSubscriptions()->exists()) {
            return;
        }

        try {
            $customer->notify(new OrderConfirmedNotification($transaction));
        } catch (Exception $e) {
            Log::error("Failed to send web push to customer {$customer->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Transaction "updated" event.
     * Auto-create Payment record berdasarkan workflow_status dan payment_timing.
     * Broadcast event untuk real-time notifications.
     * Send web push ke customer ketika order dikonfirmasi.
     */
    public function updated(Transaction $transaction): void
    {
        // Send web push ke customer ketika order dikonfirmasi oleh kurir
        if ($transaction->wasChanged('workflow_status') && $transaction->workflow_status === 'confirmed') {
            $this->sendWebPushToCustomer($transaction);
        }

        // Auto-create Payment ketika workflow_status berubah ke status tertentu
        if ($transaction->wasChanged('workflow_status')) {
            $paymentTiming = TransactionHelper::getPaymentTiming($transaction);
            $totalPrice = TransactionHelper::getTotalPrice($transaction);
            $courierId = $transaction->courier_id;

            // Case 1: Bayar Saat Jemput (original setting)
            if (
                $paymentTiming === 'on_pickup' &&
                $transaction->workflow_status === 'picked_up' &&
                !empty($courierId) &&
                $totalPrice > 0
            ) {
                $existingPayment = Payment::where('transaction_id', $transaction->id)->first();

                if (!$existingPayment) {
                    Payment::create([
                        'transaction_id' => $transaction->id,
                        'courier_id' => $courierId,
                        'amount' => $totalPrice,
                        'data' => [
                            'payment_date' => now()->toIso8601String(),
                            'method' => 'cash',
                            'notes' => 'Pembayaran saat jemput - Auto-generated',
                        ],
                    ]);
                }
            }

            // Case 2: Bayar Saat Antar (original setting)
            if (
                $paymentTiming === 'on_delivery' &&
                $transaction->workflow_status === 'out_for_delivery' &&
                !empty($courierId) &&
                $totalPrice > 0
            ) {
                $existingPayment = Payment::where('transaction_id', $transaction->id)->first();

                if (!$existingPayment) {
                    Payment::create([
                        'transaction_id' => $transaction->id,
                        'courier_id' => $courierId,
                        'amount' => $totalPrice,
                        'data' => [
                            'payment_date' => now()->toIso8601String(),
                            'method' => 'cash',
                            'notes' => 'Pembayaran saat antar - Auto-generated',
                        ],
                    ]);
                }
            }

            // Case 3: Status berubah ke 'picked_up' (Dijemput) - auto create payment regardless of original timing
            // This handles cases where courier marks order as picked up but payment wasn't set to on_pickup
            if (
                $transaction->workflow_status === 'picked_up' &&
                !empty($courierId) &&
                $totalPrice > 0
            ) {
                $existingPayment = Payment::where('transaction_id', $transaction->id)->first();

                if (!$existingPayment) {
                    // Determine payment method based on original timing
                    $paymentMethod = $paymentTiming === 'on_delivery' ? 'cash_on_delivery' : 'cash';
                    $notes = $paymentTiming === 'on_delivery'
                        ? 'Pembayaran saat jemput (ubah dari saat antar) - Auto-generated'
                        : 'Pembayaran saat jemput - Auto-generated';

                    Payment::create([
                        'transaction_id' => $transaction->id,
                        'courier_id' => $courierId,
                        'amount' => $totalPrice,
                        'data' => [
                            'payment_date' => now()->toIso8601String(),
                            'method' => $paymentMethod,
                            'original_timing' => $paymentTiming,
                            'notes' => $notes,
                        ],
                    ]);
                }
            }
        }

        // Broadcast event dengan action 'updated'
        event(new TransactionEvents($transaction->load(['customer', 'courier', 'location']), 'updated'));
    }

    /**
     * Handle the Transaction "deleted" event.
     * Broadcast event untuk real-time notifications.
     */
    public function deleted(Transaction $transaction): void
    {
        // Broadcast event dengan action 'deleted'
        event(new TransactionEvents($transaction->load(['customer', 'courier', 'location']), 'deleted'));
    }
}

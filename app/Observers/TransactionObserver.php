<?php

declare(strict_types=1);

namespace App\Observers;

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
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Generate nomor invoice unik
     * Format: INV/YYYYMMDD/XXXX
     * Contoh: INV/20251010/0001
     */
    public function generateInvoiceNumber(): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "INV/{$date}/";

        // Ambil invoice terakhir hari ini
        $lastInvoice = Transaction::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Ekstrak nomor bagian terakhir dan tambahkan 1
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Cek apakah nomor invoice sudah ada
     */
    public function invoiceExists(string $invoiceNumber): bool
    {
        return Transaction::where('invoice_number', $invoiceNumber)->exists();
    }

    /**
     * Dapatkan invoice berdasarkan nomor
     */
    public function getInvoice(string $invoiceNumber): ?Transaction
    {
        return Transaction::where('invoice_number', $invoiceNumber)->first();
    }
}

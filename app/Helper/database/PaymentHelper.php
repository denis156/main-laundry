<?php

declare(strict_types=1);

namespace App\Helper\Database;

use Carbon\Carbon;
use App\Models\Payment;

/**
 * Payment Helper
 *
 * Helper untuk menangani data JSONB di tabel payments.
 *
 * JSONB Structure:
 * - payment_date: datetime
 * - proof_url: string
 * - method: string (cash, transfer, qris)
 * - notes: string
 */
class PaymentHelper
{
    public static function getPaymentDate(Payment $payment): ?Carbon
    {
        $dateStr = $payment->data['payment_date'] ?? null;
        return $dateStr ? Carbon::parse($dateStr) : null;
    }

    public static function getFormattedPaymentDate(Payment $payment): string
    {
        $paymentDate = self::getPaymentDate($payment);
        return $paymentDate?->format('d M Y, H:i') ?? '-';
    }

    public static function getProofUrl(Payment $payment): ?string
    {
        return $payment->data['proof_url'] ?? null;
    }

    public static function getMethod(Payment $payment): string
    {
        return $payment->data['method'] ?? 'cash';
    }

    public static function getNotes(Payment $payment): ?string
    {
        return $payment->data['notes'] ?? null;
    }

    public static function getFormattedAmount(Payment $payment): string
    {
        return 'Rp ' . number_format((float) $payment->amount, 0, ',', '.');
    }

    public static function setPaymentData(
        Payment $payment,
        string $paymentDate,
        ?string $proofUrl = null,
        string $method = 'cash',
        ?string $notes = null
    ): void {
        $payment->data = [
            'payment_date' => $paymentDate,
            'proof_url' => $proofUrl,
            'method' => $method,
            'notes' => $notes,
        ];
    }
}

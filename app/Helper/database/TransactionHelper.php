<?php

declare(strict_types=1);

namespace App\Helper\Database;

use Carbon\Carbon;
use App\Models\Transaction;

/**
 * Transaction Helper
 *
 * Helper untuk menangani data JSONB di tabel transactions.
 *
 * JSONB Structure:
 * - items: [{service_id, service_name, weight, price_per_kg, subtotal}]
 * - pricing: {total_price, payment_timing: on_pickup|on_delivery}
 * - customer_address: {district_code, district_name, village_code, village_name, detail_address}
 * - notes: string
 * - metadata: {tags, custom_fields}
 * - tracking: {tracking_token, tracking_url}
 * - timeline: [{status, timestamp, notes}]
 * - anti_bot: {customer_ip, user_agent, form_loaded_at}
 */
class TransactionHelper
{
    public static function getItems(Transaction $transaction): array
    {
        return $transaction->data['items'] ?? [];
    }

    public static function getTotalPrice(Transaction $transaction): float
    {
        return (float) ($transaction->data['pricing']['total_price'] ?? 0);
    }

    public static function getFormattedTotalPrice(Transaction $transaction): string
    {
        $price = self::getTotalPrice($transaction);
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    public static function getPaymentTiming(Transaction $transaction): string
    {
        return $transaction->data['pricing']['payment_timing'] ?? 'on_delivery';
    }

    public static function getPaymentTimingText(Transaction $transaction): string
    {
        return self::getPaymentTiming($transaction) === 'on_pickup'
            ? 'Bayar Saat Jemput'
            : 'Bayar Saat Antar';
    }

    public static function getCustomerAddress(Transaction $transaction): array
    {
        return $transaction->data['customer_address'] ?? [];
    }

    public static function getNotes(Transaction $transaction): ?string
    {
        return $transaction->data['notes'] ?? null;
    }

    public static function getTrackingToken(Transaction $transaction): ?string
    {
        return $transaction->data['tracking']['tracking_token'] ?? null;
    }

    public static function getTimeline(Transaction $transaction): array
    {
        return $transaction->data['timeline'] ?? [];
    }

    public static function addTimelineEntry(
        Transaction $transaction,
        string $status,
        ?string $notes = null
    ): void {
        $data = $transaction->data ?? [];
        $timeline = $data['timeline'] ?? [];

        $timeline[] = [
            'status' => $status,
            'timestamp' => Carbon::now()->toIso8601String(),
            'notes' => $notes,
        ];

        $data['timeline'] = $timeline;
        $transaction->data = $data;
    }

    public static function getOrderDate(Transaction $transaction): ?Carbon
    {
        $timeline = self::getTimeline($transaction);
        if (empty($timeline)) {
            return null;
        }

        $firstEntry = $timeline[0];
        return isset($firstEntry['timestamp'])
            ? Carbon::parse($firstEntry['timestamp'])
            : null;
    }

    public static function getFormattedOrderDate(Transaction $transaction): string
    {
        $orderDate = self::getOrderDate($transaction);
        return $orderDate?->format('d M Y, H:i') ?? '-';
    }
}

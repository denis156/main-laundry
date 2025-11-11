<?php

declare(strict_types=1);

namespace App\Helper\Database;

use App\Models\Service;

/**
 * Service Helper
 *
 * Helper untuk menangani data JSONB di tabel services.
 *
 * JSONB Structure:
 * - service_type: string (cuci_kering, cuci_setrika, dll)
 * - pricing: {price_per_kg, currency}
 * - pricing_tiers: [{min_kg, max_kg, price_per_kg}]
 * - duration_days: int
 * - features: [string]
 * - includes: [string]
 * - restrictions: [string]
 * - materials_used: [string]
 * - icon: string
 * - color: string
 * - badge_settings: {text, color}
 */
class ServiceHelper
{
    public static function getServiceType(Service $service): string
    {
        return $service->data['service_type'] ?? 'standard';
    }

    public static function getPricing(Service $service): array
    {
        return $service->data['pricing'] ?? [];
    }

    public static function getPricePerKg(Service $service): float
    {
        return (float) ($service->data['pricing']['price_per_kg'] ?? 0);
    }

    public static function getDurationDays(Service $service): int
    {
        return (int) ($service->data['duration_days'] ?? 1);
    }

    public static function getDurationHours(Service $service): int
    {
        return (int) ($service->data['duration_hours'] ?? 72);
    }

    public static function getFormattedDuration(Service $service): string
    {
        $hours = self::getDurationHours($service);

        if ($hours < 24) {
            return $hours . ' Jam';
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        if ($remainingHours === 0) {
            return $days . ' Hari';
        }

        return $days . ' Hari ' . $remainingHours . ' Jam';
    }

    public static function getFeatures(Service $service): array
    {
        return $service->data['features'] ?? [];
    }

    public static function getFormattedPrice(Service $service): string
    {
        $price = self::getPricePerKg($service);
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    public static function getBadgeSettings(Service $service): ?array
    {
        return $service->data['badge_settings'] ?? null;
    }

    /**
     * Get pricing unit (per_kg atau per_item)
     */
    public static function getPricingUnit(Service $service): string
    {
        return $service->data['pricing']['unit'] ?? 'per_kg';
    }

    /**
     * Check if service is per kg (requires clothing types & weight)
     */
    public static function isPerKg(Service $service): bool
    {
        return self::getPricingUnit($service) === 'per_kg';
    }

    /**
     * Check if service is per item (no clothing types needed)
     */
    public static function isPerItem(Service $service): bool
    {
        return self::getPricingUnit($service) === 'per_item';
    }

    /**
     * Get price per item (for per_item services)
     */
    public static function getPricePerItem(Service $service): float
    {
        return (float) ($service->data['pricing']['price_per_item'] ?? 0);
    }

    /**
     * Get formatted price based on unit
     */
    public static function getFormattedPriceWithUnit(Service $service): string
    {
        if (self::isPerItem($service)) {
            $price = self::getPricePerItem($service);
            return 'Rp ' . number_format($price, 0, ',', '.') . '/item';
        }

        $price = self::getPricePerKg($service);
        return 'Rp ' . number_format($price, 0, ',', '.') . '/kg';
    }
}

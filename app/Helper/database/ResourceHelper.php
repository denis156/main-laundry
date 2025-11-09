<?php

declare(strict_types=1);

namespace App\Helper\Database;

use App\Models\Resource;

/**
 * Resource Helper
 *
 * Helper untuk menangani data JSONB di tabel resources.
 * 
 * JSONB Structure untuk Equipment:
 * - brand: string
 * - serial_number: string
 * - purchase_price: float
 * - purchase_date: date
 * - status: string (baik, rusak, maintenance)
 * - maintenance: {last_date, last_cost, next_date, history: [...]}
 * 
 * JSONB Structure untuk Material:
 * - unit: string (kg, liter, pcs)
 * - stocks: {initial, current, minimum}
 * - pricing: {price_per_unit, supplier}
 * - expired_date: date|null
 * - stock_history: [{type, quantity, date, notes}]
 */
class ResourceHelper
{
    public static function isEquipment(Resource $resource): bool
    {
        return $resource->type === 'equipment';
    }

    public static function isMaterial(Resource $resource): bool
    {
        return $resource->type === 'material';
    }

    // Equipment specific
    public static function getBrand(Resource $resource): ?string
    {
        return $resource->data['brand'] ?? null;
    }

    public static function getStatus(Resource $resource): string
    {
        return $resource->data['status'] ?? 'baik';
    }

    // Material specific
    public static function getUnit(Resource $resource): ?string
    {
        return $resource->data['unit'] ?? null;
    }

    public static function getCurrentStock(Resource $resource): float
    {
        return (float) ($resource->data['stocks']['current'] ?? 0);
    }

    public static function getMinimumStock(Resource $resource): float
    {
        return (float) ($resource->data['stocks']['minimum'] ?? 0);
    }

    public static function isLowStock(Resource $resource): bool
    {
        if (!self::isMaterial($resource)) {
            return false;
        }
        return self::getCurrentStock($resource) <= self::getMinimumStock($resource);
    }
}

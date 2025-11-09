<?php

declare(strict_types=1);

namespace App\Helper\Database;

use App\Models\ClothingType;

/**
 * ClothingType Helper
 *
 * Helper untuk menangani data JSONB di tabel clothing_types.
 *
 * JSONB Structure:
 * - description: string (deskripsi jenis pakaian)
 * - care_instructions: array (instruksi perawatan khusus)
 */
class ClothingTypeHelper
{
    public static function getDescription(ClothingType $clothingType): ?string
    {
        return $clothingType->data['description'] ?? null;
    }

    public static function getCareInstructions(ClothingType $clothingType): array
    {
        return $clothingType->data['care_instructions'] ?? [];
    }

    public static function isActive(ClothingType $clothingType): bool
    {
        return $clothingType->is_active ?? true;
    }
}

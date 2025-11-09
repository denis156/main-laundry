<?php

declare(strict_types=1);

namespace App\Helper\Database;

use App\Models\Courier;
use Illuminate\Support\Facades\Storage;

/**
 * Courier Helper
 *
 * Helper untuk menangani data JSONB di tabel couriers.
 *
 * JSONB Structure:
 * - name: string
 * - phone: string
 * - vehicle_number: string
 * - avatar_url: string|null
 * - is_active: boolean
 * - preferences: {notification_enabled}
 */
class CourierHelper
{
    public static function getName(Courier $courier): string
    {
        return $courier->data['name'] ?? 'N/A';
    }

    public static function setName(Courier $courier, string $name): void
    {
        $data = $courier->data ?? [];
        $data['name'] = $name;
        $courier->data = $data;
    }

    public static function getPhone(Courier $courier): ?string
    {
        return $courier->data['phone'] ?? null;
    }

    public static function getVehicleNumber(Courier $courier): ?string
    {
        return $courier->data['vehicle_number'] ?? null;
    }

    public static function isActive(Courier $courier): bool
    {
        return $courier->data['is_active'] ?? true;
    }

    public static function getAvatarUrl(Courier $courier): ?string
    {
        return $courier->data['avatar_url'] ?? null;
    }

    /**
     * Generate initials dari nama customer
     */
    public static function getInitials(Courier $courier): string
    {
        $name = trim(self::getName($courier));
        $words = preg_split('/\s+/', $name);

        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, 2));
        }

        $first = strtoupper(substr($words[0], 0, 1));
        $last = strtoupper(substr(end($words), 0, 1));
        return $first . $last;
    }

    /**
     * Get avatar URL untuk Filament
     */
    public static function getFilamentAvatarUrl(Courier $courier): string
    {
        $avatarUrl = self::getAvatarUrl($courier);

        // Avatar lokal
        if (!empty($avatarUrl) && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
            if (Storage::disk('public')->exists($avatarUrl)) {
                return asset('storage/' . $avatarUrl);
            }
        }

        // Avatar dari URL (Google)
        if (!empty($avatarUrl) && filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
            return $avatarUrl;
        }

        // Default avatar
        return self::generateDefaultAvatar($courier);
    }

    public static function generateDefaultAvatar(Courier $courier): string
    {
        $name = urlencode(self::getName($courier));
        return "https://ui-avatars.com/api/?name={$name}&background=000000&color=ffffff&size=128";
    }
}

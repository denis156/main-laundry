<?php

declare(strict_types=1);

namespace App\Helper\Database;

use App\Models\Customer;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Customer Helper
 *
 * Helper untuk menangani data JSONB di tabel customers.
 * 
 * JSONB Structure:
 * - name: string
 * - addresses: [{type, district_code, district_name, village_code, village_name, detail_address, is_default}]
 * - preferences: {notification_enabled, language}
 * - google_oauth: {google_id, google_token, google_refresh_token}
 * - member: boolean
 * - avatar_url: string
 */
class CustomerHelper
{
    public static function getName(Customer $customer): string
    {
        return $customer->data['name'] ?? 'N/A';
    }

    public static function setName(Customer $customer, string $name): void
    {
        $data = $customer->data ?? [];
        $data['name'] = $name;
        $customer->data = $data;
    }

    public static function getAddresses(Customer $customer): array
    {
        return $customer->data['addresses'] ?? [];
    }

    public static function getDefaultAddress(Customer $customer): ?array
    {
        $addresses = self::getAddresses($customer);
        foreach ($addresses as $address) {
            if ($address['is_default'] ?? false) {
                return $address;
            }
        }
        return $addresses[0] ?? null;
    }

    public static function isMember(Customer $customer): bool
    {
        return $customer->data['member'] ?? false;
    }

    public static function getAvatarUrl(Customer $customer): ?string
    {
        return $customer->data['avatar_url'] ?? null;
    }

    /**
     * Generate initials dari nama customer
     */
    public static function getInitials(Customer $customer): string
    {
        $name = trim(self::getName($customer));
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
    public static function getFilamentAvatarUrl(Customer $customer): string
    {
        $avatarUrl = self::getAvatarUrl($customer);
        
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
        return self::generateDefaultAvatar($customer);
    }

    private static function generateDefaultAvatar(Customer $customer): string
    {
        $name = urlencode(self::getName($customer));
        return "https://ui-avatars.com/api/?name={$name}&background=000000&color=ffffff&size=128";
    }
}

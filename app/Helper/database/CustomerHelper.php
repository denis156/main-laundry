<?php

declare(strict_types=1);

namespace App\Helper\Database;

use App\Models\Customer;
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

    public static function getDefaultAddressString(Customer $customer): string
    {
        $address = self::getDefaultAddress($customer);
        if (!$address) {
            return 'Belum ada alamat';
        }

        return ($address['detail_address'] ?? '') . ', ' .
               ($address['village_name'] ?? '') . ', ' .
               ($address['district_name'] ?? '');
    }

    public static function getFullAddressString(array $address): string
    {
        if (empty($address)) {
            return 'Belum ada alamat';
        }

        return ($address['full_address'] ?? '') ?:
               (($address['detail_address'] ?? '') . ', ' .
               ($address['village_name'] ?? '') . ', ' .
               ($address['district_name'] ?? ''));
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

    public static function generateDefaultAvatar(Customer $customer): string
    {
        $name = urlencode(self::getName($customer));
        return "https://ui-avatars.com/api/?name={$name}&background=000000&color=ffffff&size=128";
    }

    /**
     * Auto-fill customer addresses data yang kosong
     * Method ini akan:
     * 1. Auto-fill district_name dari district_code untuk setiap address
     * 2. Auto-fill village_name dari village_code untuk setiap address
     * 3. Auto-generate full_address jika semua data lengkap
     */
    public static function autoFillCustomerAddresses(Customer $customer): void
    {
        $data = $customer->data ?? [];
        $addresses = $data['addresses'] ?? [];

        if (empty($addresses)) {
            return;
        }

        foreach ($addresses as $index => $address) {
            // Auto-fill district_name jika kosong tapi ada district_code
            if (!empty($address['district_code']) && empty($address['district_name'])) {
                $districts = \App\Helper\WilayahHelper::getKendariDistricts();
                $district = collect($districts)->firstWhere('code', $address['district_code']);
                if ($district) {
                    $addresses[$index]['district_name'] = $district['name'];
                }
            }

            // Auto-fill village_name jika kosong tapi ada village_code
            if (!empty($address['village_code']) && empty($address['village_name'])) {
                $districtCode = $address['district_code'] ?? null;
                if ($districtCode) {
                    $villages = \App\Helper\WilayahHelper::getVillagesByDistrict($districtCode);
                    $village = collect($villages)->firstWhere('code', $address['village_code']);
                    if ($village) {
                        $addresses[$index]['village_name'] = $village['name'];
                    }
                }
            }

            // Auto-generate full_address jika semua data lengkap
            $detailAddress = $addresses[$index]['detail_address'] ?? null;
            $villageName = $addresses[$index]['village_name'] ?? null;
            $districtName = $addresses[$index]['district_name'] ?? null;

            if ($detailAddress && $villageName && $districtName && empty($addresses[$index]['full_address'])) {
                $addresses[$index]['full_address'] = \App\Helper\WilayahHelper::formatFullAddress(
                    $detailAddress,
                    $villageName,
                    $districtName
                );
            }
        }

        $data['addresses'] = $addresses;
        $customer->data = $data;
    }
}

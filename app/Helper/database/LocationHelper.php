<?php

declare(strict_types=1);

namespace App\Helper\Database;

use App\Helper\WilayahHelper;
use App\Models\Location;

/**
 * Location Helper
 *
 * Helper untuk menangani data JSONB di tabel locations.
 */
class LocationHelper
{
    public static function getLocationAddress(Location $location): array
    {
        return $location->data['location'] ?? [];
    }

    public static function setLocationAddress(
        Location $location,
        string $districtCode,
        string $districtName,
        string $villageCode,
        string $villageName,
        string $detailAddress,
        ?array $coordinates = null
    ): void {
        $data = $location->data ?? [];
        $data['location'] = [
            'district_code' => $districtCode,
            'district_name' => $districtName,
            'village_code' => $villageCode,
            'village_name' => $villageName,
            'detail_address' => $detailAddress,
            'address' => WilayahHelper::formatFullAddress($detailAddress, $villageName, $districtName),
            'coordinates' => $coordinates,
        ];
        $location->data = $data;
    }

    public static function getCoverageArea(Location $location): array
    {
        return $location->data['coverage_area'] ?? [];
    }

    public static function getContact(Location $location): array
    {
        return $location->data['contact'] ?? [];
    }

    public static function isResort(Location $location): bool
    {
        return $location->type === 'resort';
    }

    public static function isPos(Location $location): bool
    {
        return $location->type === 'pos';
    }
}

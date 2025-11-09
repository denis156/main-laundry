<?php

declare(strict_types=1);

namespace App\Helper\Database;

use App\Helper\WilayahHelper;
use App\Models\Location;

/**
 * Location Helper
 *
 * Helper untuk menangani data JSONB di tabel locations.
 *
 * JSONB Structure:
 * - location: {district_code, district_name, village_code, village_name, detail_address, address, coordinates}
 * - coverage_area: [array of district names for resort OR array of village names for pos]
 * - operating_hours: {weekday: {open, close}, weekend: {open, close}}
 * - contact: {phone, email, pic_name}
 * - facilities: [array of facility names]
 * - capacity: {max_daily_kg}
 * - metadata: {created_reason, notes, etc}
 */
class LocationHelper
{
    /**
     * Get location address data
     */
    public static function getLocationAddress(Location $location): array
    {
        return $location->data['location'] ?? [];
    }

    /**
     * Get district code from location address
     */
    public static function getDistrictCode(Location $location): ?string
    {
        return $location->data['location']['district_code'] ?? null;
    }

    /**
     * Get district name from location address
     */
    public static function getDistrictName(Location $location): ?string
    {
        return $location->data['location']['district_name'] ?? null;
    }

    /**
     * Get village code from location address
     */
    public static function getVillageCode(Location $location): ?string
    {
        return $location->data['location']['village_code'] ?? null;
    }

    /**
     * Get village name from location address
     */
    public static function getVillageName(Location $location): ?string
    {
        return $location->data['location']['village_name'] ?? null;
    }

    /**
     * Get detail address from location address
     */
    public static function getDetailAddress(Location $location): ?string
    {
        return $location->data['location']['detail_address'] ?? null;
    }

    /**
     * Get full address from location address
     */
    public static function getFullAddress(Location $location): ?string
    {
        return $location->data['location']['address'] ?? null;
    }

    /**
     * Get coordinates from location address
     */
    public static function getCoordinates(Location $location): ?array
    {
        return $location->data['location']['coordinates'] ?? null;
    }

    /**
     * Set location address data
     */
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

    /**
     * Get coverage area
     * Untuk resort: array of district names (kecamatan yang dilayani)
     * Untuk pos: array of village names (kelurahan yang dilayani)
     */
    public static function getCoverageArea(Location $location): array
    {
        return $location->data['coverage_area'] ?? [];
    }

    /**
     * Set coverage area
     * Untuk resort: array of district names (kecamatan yang dipilih untuk dilayani)
     * Untuk pos: array of village names (kelurahan yang dipilih untuk dilayani)
     */
    public static function setCoverageArea(Location $location, array $coverageArea): void
    {
        $data = $location->data ?? [];
        $data['coverage_area'] = array_values(array_unique($coverageArea));
        $location->data = $data;
    }

    /**
     * Add area to coverage area
     * Untuk resort: tambah nama kecamatan
     * Untuk pos: tambah nama kelurahan
     */
    public static function addCoverageArea(Location $location, string $areaName): void
    {
        $data = $location->data ?? [];
        $coverageArea = $data['coverage_area'] ?? [];

        if (!in_array($areaName, $coverageArea)) {
            $coverageArea[] = $areaName;
            $data['coverage_area'] = array_values($coverageArea);
            $location->data = $data;
        }
    }

    /**
     * Remove area from coverage area
     */
    public static function removeCoverageArea(Location $location, string $areaName): void
    {
        $data = $location->data ?? [];
        $coverageArea = $data['coverage_area'] ?? [];

        $coverageArea = array_filter($coverageArea, fn($area) => $area !== $areaName);
        $data['coverage_area'] = array_values($coverageArea);
        $location->data = $data;
    }

    /**
     * Check if location covers specific area
     * Untuk resort: cek apakah melayani kecamatan tertentu
     * Untuk pos: cek apakah melayani kelurahan tertentu
     */
    public static function coversArea(Location $location, string $areaName): bool
    {
        $coverageArea = self::getCoverageArea($location);
        return in_array($areaName, $coverageArea);
    }

    /**
     * Get coverage area as formatted string
     */
    public static function getCoverageAreaString(Location $location): string
    {
        $coverageArea = self::getCoverageArea($location);
        return empty($coverageArea) ? '-' : implode(', ', $coverageArea);
    }

    /**
     * Get coverage area type description
     * Resort: "Kecamatan yang dilayani"
     * Pos: "Kel/Desa yang dilayani"
     */
    public static function getCoverageAreaType(Location $location): string
    {
        return self::isResort($location) ? 'Kecamatan' : 'Kel/Desa';
    }

    /**
     * Get contact information
     */
    public static function getContact(Location $location): array
    {
        return $location->data['contact'] ?? [];
    }

    /**
     * Get phone number from contact
     */
    public static function getPhone(Location $location): ?string
    {
        return $location->data['contact']['phone'] ?? null;
    }

    /**
     * Get email from contact
     */
    public static function getEmail(Location $location): ?string
    {
        return $location->data['contact']['email'] ?? null;
    }

    /**
     * Get PIC name from contact
     */
    public static function getPicName(Location $location): ?string
    {
        return $location->data['contact']['pic_name'] ?? null;
    }

    /**
     * Check if location is a resort
     */
    public static function isResort(Location $location): bool
    {
        return $location->type === 'resort';
    }

    /**
     * Check if location is a pos
     */
    public static function isPos(Location $location): bool
    {
        return $location->type === 'pos';
    }

    /**
     * Check if location is active
     */
    public static function isActive(Location $location): bool
    {
        return $location->is_active ?? false;
    }

    /**
     * Auto-fill location data yang kosong
     * Method ini akan:
     * 1. Auto-fill district_name dari district_code
     * 2. Auto-fill village_name dari village_code
     * 3. Auto-generate full address jika semua data lengkap
     */
    public static function autoFillLocationData(Location $location): void
    {
        $data = $location->data ?? [];

        // Auto-fill district_name jika kosong tapi ada district_code
        if (!empty($data['location']['district_code']) && empty($data['location']['district_name'])) {
            $districts = WilayahHelper::getKendariDistricts();
            $district = collect($districts)->firstWhere('code', $data['location']['district_code']);
            if ($district) {
                $data['location']['district_name'] = $district['name'];
            }
        }

        // Auto-fill village_name jika kosong tapi ada village_code
        if (!empty($data['location']['village_code']) && empty($data['location']['village_name'])) {
            $districtCode = $data['location']['district_code'] ?? null;
            if ($districtCode) {
                $villages = WilayahHelper::getVillagesByDistrict($districtCode);
                $village = collect($villages)->firstWhere('code', $data['location']['village_code']);
                if ($village) {
                    $data['location']['village_name'] = $village['name'];
                }
            }
        }

        // Auto-generate address jika semua data lengkap
        $detailAddress = $data['location']['detail_address'] ?? null;
        $villageName = $data['location']['village_name'] ?? null;
        $districtName = $data['location']['district_name'] ?? null;

        if ($detailAddress && $villageName && $districtName && empty($data['location']['address'])) {
            $data['location']['address'] = WilayahHelper::formatFullAddress($detailAddress, $villageName, $districtName);
        }

        $location->data = $data;
    }
}

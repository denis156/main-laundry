<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WilayahService
{
    private const BASE_URL = 'https://wilayah.id/api';

    // Sulawesi Tenggara province code
    private const SULAWESI_TENGGARA_CODE = '74';

    // Kota Kendari regency code
    private const KOTA_KENDARI_CODE = '74.71';

    /**
     * Get all districts (kecamatan) di Kota Kendari
     */
    public function getKendariDistricts(): array
    {
        return Cache::remember('wilayah_kendari_districts', now()->addDays(30), function () {
            try {
                $response = Http::timeout(10)
                    ->get(self::BASE_URL . '/districts/' . self::KOTA_KENDARI_CODE . '.json');

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data'] ?? [];
                }

                return [];
            } catch (\Exception $e) {
                logger()->error('Failed to fetch Kendari districts', [
                    'error' => $e->getMessage(),
                ]);
                return [];
            }
        });
    }

    /**
     * Get all villages (kelurahan) dari kecamatan tertentu
     */
    public function getVillagesByDistrict(string $districtCode): array
    {
        return Cache::remember('wilayah_villages_' . $districtCode, now()->addDays(30), function () use ($districtCode) {
            try {
                $response = Http::timeout(10)
                    ->get(self::BASE_URL . '/villages/' . $districtCode . '.json');

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data'] ?? [];
                }

                return [];
            } catch (\Exception $e) {
                logger()->error('Failed to fetch villages', [
                    'district_code' => $districtCode,
                    'error' => $e->getMessage(),
                ]);
                return [];
            }
        });
    }

    /**
     * Get province name (Sulawesi Tenggara)
     */
    public function getProvinceName(): string
    {
        return 'Sulawesi Tenggara';
    }

    /**
     * Get regency name (Kota Kendari)
     */
    public function getRegencyName(): string
    {
        return 'Kota Kendari';
    }

    /**
     * Format full address
     */
    public function formatFullAddress(
        string $detailAddress,
        string $villageName,
        string $districtName
    ): string {
        return sprintf(
            '%s, %s, %s, %s, %s',
            $detailAddress,
            $villageName,
            $districtName,
            $this->getRegencyName(),
            $this->getProvinceName()
        );
    }
}

<?php

namespace App\Observers;

use App\Models\Resort;
use App\Services\WilayahService;

class ResortObserver
{
    public function creating(Resort $resort): void
    {
        $this->syncWilayahNames($resort);
        $this->generateFullAddress($resort);
    }

    public function updating(Resort $resort): void
    {
        $this->syncWilayahNames($resort);
        $this->generateFullAddress($resort);
    }

    private function syncWilayahNames(Resort $resort): void
    {
        $wilayahService = app(WilayahService::class);

        if (!empty($resort->district_code) && empty($resort->district_name)) {
            $districts = $wilayahService->getKendariDistricts();
            $district = collect($districts)->firstWhere('code', $resort->district_code);
            if ($district) {
                $resort->district_name = $district['name'];
            }
        }

        if (!empty($resort->village_code) && empty($resort->village_name) && !empty($resort->district_code)) {
            $villages = $wilayahService->getVillagesByDistrict($resort->district_code);
            $village = collect($villages)->firstWhere('code', $resort->village_code);
            if ($village) {
                $resort->village_name = $village['name'];
            }
        }
    }

    private function generateFullAddress(Resort $resort): void
    {
        $addressParts = [];

        if (!empty($resort->detail_address)) {
            $addressParts[] = $resort->detail_address;
        }

        if (!empty($resort->village_name)) {
            $addressParts[] = $resort->village_name;
        }

        if (!empty($resort->district_name)) {
            $addressParts[] = $resort->district_name;
        }

        $addressParts[] = 'Kota Kendari';
        $addressParts[] = 'Sulawesi Tenggara';

        $resort->address = implode(', ', array_filter($addressParts));
    }
}

<?php

namespace App\Observers;

use App\Models\Pos;
use App\Services\WilayahService;

class PosObserver
{
    public function creating(Pos $pos): void
    {
        $this->syncWilayahNames($pos);
        $this->generateFullAddress($pos);
    }

    public function updating(Pos $pos): void
    {
        $this->syncWilayahNames($pos);
        $this->generateFullAddress($pos);
    }

    private function syncWilayahNames(Pos $pos): void
    {
        $wilayahService = app(WilayahService::class);

        if (!empty($pos->district_code) && empty($pos->district_name)) {
            $districts = $wilayahService->getKendariDistricts();
            $district = collect($districts)->firstWhere('code', $pos->district_code);
            if ($district) {
                $pos->district_name = $district['name'];
            }
        }

        if (!empty($pos->village_code) && empty($pos->village_name) && !empty($pos->district_code)) {
            $villages = $wilayahService->getVillagesByDistrict($pos->district_code);
            $village = collect($villages)->firstWhere('code', $pos->village_code);
            if ($village) {
                $pos->village_name = $village['name'];
            }
        }
    }

    private function generateFullAddress(Pos $pos): void
    {
        $addressParts = [];

        if (!empty($pos->detail_address)) {
            $addressParts[] = $pos->detail_address;
        }

        if (!empty($pos->village_name)) {
            $addressParts[] = $pos->village_name;
        }

        if (!empty($pos->district_name)) {
            $addressParts[] = $pos->district_name;
        }

        $addressParts[] = 'Kota Kendari';
        $addressParts[] = 'Sulawesi Tenggara';

        $pos->address = implode(', ', array_filter($addressParts));
    }
}

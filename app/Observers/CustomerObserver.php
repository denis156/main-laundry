<?php

namespace App\Observers;

use App\Models\Customer;
use App\Services\WilayahService;

class CustomerObserver
{
    public function creating(Customer $customer): void
    {
        $this->syncWilayahNames($customer);
        $this->generateFullAddress($customer);
    }

    public function updating(Customer $customer): void
    {
        $this->syncWilayahNames($customer);
        $this->generateFullAddress($customer);
    }

    private function syncWilayahNames(Customer $customer): void
    {
        $wilayahService = app(WilayahService::class);

        if (!empty($customer->district_code) && empty($customer->district_name)) {
            $districts = $wilayahService->getKendariDistricts();
            $district = collect($districts)->firstWhere('code', $customer->district_code);
            if ($district) {
                $customer->district_name = $district['name'];
            }
        }

        if (!empty($customer->village_code) && empty($customer->village_name) && !empty($customer->district_code)) {
            $villages = $wilayahService->getVillagesByDistrict($customer->district_code);
            $village = collect($villages)->firstWhere('code', $customer->village_code);
            if ($village) {
                $customer->village_name = $village['name'];
            }
        }
    }

    private function generateFullAddress(Customer $customer): void
    {
        $addressParts = [];

        if (!empty($customer->detail_address)) {
            $addressParts[] = $customer->detail_address;
        }

        if (!empty($customer->village_name)) {
            $addressParts[] = $customer->village_name;
        }

        if (!empty($customer->district_name)) {
            $addressParts[] = $customer->district_name;
        }

        $addressParts[] = 'Kota Kendari';
        $addressParts[] = 'Sulawesi Tenggara';

        $customer->address = implode(', ', array_filter($addressParts));
    }
}

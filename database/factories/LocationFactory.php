<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Location;
use App\Helper\WilayahHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Generate Indonesian phone number format
     */
    private function generateIndonesianPhone(): string
    {
        return fake()->numerify('8##########');
    }

    /**
     * Generate coverage area untuk Resort (kecamatan yang dilayani)
     */
    private function generateResortCoverageArea(string $currentDistrictCode): array
    {
        $districts = WilayahHelper::getKendariDistricts();
        $coverageDistricts = [];

        // Tambahkan kecamatan resort itu sendiri
        $currentDistrict = collect($districts)->firstWhere('code', $currentDistrictCode);
        if ($currentDistrict) {
            $coverageDistricts[] = $currentDistrict['name'];
        }

        // Tambahkan 2-4 kecamatan tetangga
        $neighborDistricts = array_filter($districts, fn($d) => $d['code'] !== $currentDistrictCode);
        $selectedNeighbors = fake()->randomElements($neighborDistricts, fake()->numberBetween(2, min(4, count($neighborDistricts))));

        foreach ($selectedNeighbors as $neighbor) {
            $coverageDistricts[] = $neighbor['name'];
        }

        return array_unique($coverageDistricts);
    }

    /**
     * Generate coverage area untuk POS (kelurahan yang dilayani)
     */
    private function generatePosCoverageArea(string $currentDistrictCode): array
    {
        $coverageVillages = [];

        // Tambahkan semua kelurahan dari district POS ini
        $villages = WilayahHelper::getVillagesByDistrict($currentDistrictCode);
        foreach ($villages as $village) {
            $coverageVillages[] = $village['name'];
        }

        // Tambahkan beberapa kelurahan dari district tetangga (random 1-2 district)
        $districts = WilayahHelper::getKendariDistricts();
        $neighborDistricts = array_filter($districts, fn($d) => $d['code'] !== $currentDistrictCode);
        $selectedNeighbors = fake()->randomElements($neighborDistricts, fake()->numberBetween(1, min(2, count($neighborDistricts))));

        foreach ($selectedNeighbors as $neighbor) {
            // Ambil 1-2 kelurahan dari district tetangga
            $neighborVillages = WilayahHelper::getVillagesByDistrict($neighbor['code']);
            if (!empty($neighborVillages)) {
                $selectedVillages = fake()->randomElements($neighborVillages, fake()->numberBetween(1, min(2, count($neighborVillages))));
                foreach ($selectedVillages as $village) {
                    $coverageVillages[] = $village['name'];
                }
            }
        }

        return array_unique($coverageVillages);
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Pilih random district dari WilayahHelper
        $districts = WilayahHelper::getKendariDistricts();
        $selectedDistrict = fake()->randomElement($districts);

        // Pilih random village dari district tersebut
        $villages = WilayahHelper::getVillagesByDistrict($selectedDistrict['code']);
        $selectedVillage = fake()->randomElement($villages);

        // Tentukan tipe lokasi (default: pos)
        $type = 'pos';

        // Generate detail address
        $streets = $type === 'resort'
            ? ['Jl. Ahmad Yani', 'Jl. Sudirman', 'Jl. Veteran', 'Jl. Diponegoro']
            : ['Jl. Mawar', 'Jl. Melati', 'Jl. Anggrek', 'Jl. Dahlia', 'Jl. Kenanga', 'Jl. Flamboyan', 'Jl. Teratai'];

        $detailAddress = fake()->randomElement($streets) . ' No. ' . fake()->numberBetween(1, 500) .
            ', RT ' . str_pad((string) fake()->numberBetween(1, 20), 3, '0', STR_PAD_LEFT) .
            '/RW ' . str_pad((string) fake()->numberBetween(1, 10), 3, '0', STR_PAD_LEFT);

        // Format full address menggunakan WilayahHelper
        $fullAddress = WilayahHelper::formatFullAddress(
            $detailAddress,
            $selectedVillage['name'],
            $selectedDistrict['name']
        );

        // Generate coverage area berdasarkan tipe
        $coverageArea = $type === 'resort'
            ? $this->generateResortCoverageArea($selectedDistrict['code'])
            : $this->generatePosCoverageArea($selectedDistrict['code']);

        return [
            'type' => $type,
            'parent_id' => null,
            'name' => ucfirst($type) . ' ' . ($type === 'resort' ? $selectedDistrict['name'] : $selectedVillage['name']),
            'data' => [
                'location' => [
                    'district_code' => $selectedDistrict['code'],
                    'district_name' => $selectedDistrict['name'],
                    'village_code' => $selectedVillage['code'],
                    'village_name' => $selectedVillage['name'],
                    'detail_address' => $detailAddress,
                    'address' => $fullAddress,
                ],
                'coverage_area' => $coverageArea,
                'contact' => [
                    'phone' => $this->generateIndonesianPhone(),
                    'pic_name' => fake()->name(),
                ],
            ],
            'is_active' => fake()->boolean(90),
        ];
    }

    /**
     * Indicate that the location is a resort
     */
    public function resort(): static
    {
        return $this->state(function () {
            $districts = WilayahHelper::getKendariDistricts();
            $selectedDistrict = fake()->randomElement($districts);
            $villages = WilayahHelper::getVillagesByDistrict($selectedDistrict['code']);
            $selectedVillage = fake()->randomElement($villages);

            $detailAddress = fake()->randomElement(['Jl. Ahmad Yani', 'Jl. Sudirman', 'Jl. Veteran', 'Jl. Diponegoro']) .
                ' No. ' . fake()->numberBetween(1, 200) .
                ', RT ' . str_pad((string) fake()->numberBetween(1, 20), 3, '0', STR_PAD_LEFT) .
                '/RW ' . str_pad((string) fake()->numberBetween(1, 10), 3, '0', STR_PAD_LEFT);

            $fullAddress = WilayahHelper::formatFullAddress(
                $detailAddress,
                $selectedVillage['name'],
                $selectedDistrict['name']
            );

            $coverageArea = $this->generateResortCoverageArea($selectedDistrict['code']);

            return [
                'type' => 'resort',
                'name' => 'Resort ' . $selectedDistrict['name'],
                'data' => [
                    'location' => [
                        'district_code' => $selectedDistrict['code'],
                        'district_name' => $selectedDistrict['name'],
                        'village_code' => $selectedVillage['code'],
                        'village_name' => $selectedVillage['name'],
                        'detail_address' => $detailAddress,
                        'address' => $fullAddress,
                    ],
                    'coverage_area' => $coverageArea,
                    'contact' => [
                        'phone' => $this->generateIndonesianPhone(),
                        'pic_name' => fake()->name(),
                    ],
                ],
            ];
        });
    }

    /**
     * Indicate that the location is a pos with a parent resort
     */
    public function posWithParent(): static
    {
        return $this->state(fn () => [
            'type' => 'pos',
            'parent_id' => Location::factory()->resort(),
        ]);
    }

    /**
     * Indicate that the location is a standalone pos
     */
    public function standalone(): static
    {
        return $this->state(fn () => [
            'type' => 'pos',
            'parent_id' => null,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Data wilayah sample di Kota Kendari
     */
    private function getKendariWilayah(): array
    {
        return [
            [
                'district_code' => '74.71.01',
                'district_name' => 'Mandonga',
                'villages' => [
                    ['code' => '74.71.01.1001', 'name' => 'Mandonga'],
                    ['code' => '74.71.01.1002', 'name' => 'Wundumbolo'],
                ],
            ],
            [
                'district_code' => '74.71.02',
                'district_name' => 'Baruga',
                'villages' => [
                    ['code' => '74.71.02.1001', 'name' => 'Baruga'],
                    ['code' => '74.71.02.1002', 'name' => 'Wundulako'],
                ],
            ],
            [
                'district_code' => '74.71.03',
                'district_name' => 'Puuwatu',
                'villages' => [
                    ['code' => '74.71.03.1001', 'name' => 'Puuwatu'],
                    ['code' => '74.71.03.1002', 'name' => 'Benubenua'],
                ],
            ],
            [
                'district_code' => '74.71.04',
                'district_name' => 'Kadia',
                'villages' => [
                    ['code' => '74.71.04.1001', 'name' => 'Kadia'],
                    ['code' => '74.71.04.1002', 'name' => 'Bende'],
                ],
            ],
            [
                'district_code' => '74.71.05',
                'district_name' => 'Wua-Wua',
                'villages' => [
                    ['code' => '74.71.05.1001', 'name' => 'Wua-Wua'],
                    ['code' => '74.71.05.1002', 'name' => 'Anduonohu'],
                ],
            ],
            [
                'district_code' => '74.71.06',
                'district_name' => 'Kambu',
                'villages' => [
                    ['code' => '74.71.06.1001', 'name' => 'Kambu'],
                    ['code' => '74.71.06.1002', 'name' => 'Lalolara'],
                    ['code' => '74.71.06.1003', 'name' => 'Korumba'],
                ],
            ],
            [
                'district_code' => '74.71.07',
                'district_name' => 'Poasia',
                'villages' => [
                    ['code' => '74.71.07.1001', 'name' => 'Poasia'],
                    ['code' => '74.71.07.1002', 'name' => 'Watubangga'],
                ],
            ],
            [
                'district_code' => '74.71.08',
                'district_name' => 'Abeli',
                'villages' => [
                    ['code' => '74.71.08.1001', 'name' => 'Abeli'],
                    ['code' => '74.71.08.1002', 'name' => 'Lapulu'],
                ],
            ],
            [
                'district_code' => '74.71.09',
                'district_name' => 'Kendari Barat',
                'villages' => [
                    ['code' => '74.71.09.1001', 'name' => 'Purirano'],
                    ['code' => '74.71.09.1002', 'name' => 'Tobimeita'],
                ],
            ],
            [
                'district_code' => '74.71.10',
                'district_name' => 'Kendari',
                'villages' => [
                    ['code' => '74.71.10.1001', 'name' => 'Bungkutoko'],
                    ['code' => '74.71.10.1002', 'name' => 'Lahundape'],
                ],
            ],
        ];
    }

    /**
     * Generate coverage area untuk Resort (kecamatan yang dilayani)
     */
    private function generateResortCoverageArea(array $currentDistrict, array $allDistricts): array
    {
        $coverageDistricts = [];

        // Tambahkan kecamatan resort itu sendiri
        $coverageDistricts[] = $currentDistrict['district_name'];

        // Tambahkan 2-4 kecamatan tetangga
        $neighborDistricts = array_filter($allDistricts, fn($d) => $d['district_code'] !== $currentDistrict['district_code']);
        $selectedNeighbors = fake()->randomElements($neighborDistricts, fake()->numberBetween(2, 4));

        foreach ($selectedNeighbors as $neighbor) {
            $coverageDistricts[] = $neighbor['district_name'];
        }

        return array_unique($coverageDistricts);
    }

    /**
     * Generate coverage area untuk POS (kelurahan yang dilayani)
     */
    private function generatePosCoverageArea(array $currentDistrict, array $allDistricts): array
    {
        $coverageVillages = [];

        // Tambahkan semua kelurahan dari district pos ini
        foreach ($currentDistrict['villages'] as $village) {
            $coverageVillages[] = $village['name'];
        }

        // Tambahkan beberapa kelurahan dari district tetangga (random 1-2 district)
        $neighborDistricts = array_filter($allDistricts, fn($d) => $d['district_code'] !== $currentDistrict['district_code']);
        $selectedNeighbors = fake()->randomElements($neighborDistricts, fake()->numberBetween(1, 2));

        foreach ($selectedNeighbors as $neighbor) {
            // Ambil 1-2 kelurahan dari district tetangga
            $neighborVillages = fake()->randomElements($neighbor['villages'], fake()->numberBetween(1, 2));
            foreach ($neighborVillages as $village) {
                $coverageVillages[] = $village['name'];
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
        // Pilih random district
        $districts = $this->getKendariWilayah();
        $selectedDistrict = fake()->randomElement($districts);

        // Pilih random village dari district tersebut
        $selectedVillage = fake()->randomElement($selectedDistrict['villages']);

        // Tentukan tipe lokasi (default: pos)
        $type = 'pos';

        // Generate detail address
        $streets = $type === 'resort'
            ? ['Jl. Ahmad Yani', 'Jl. Sudirman', 'Jl. Veteran', 'Jl. Diponegoro']
            : ['Jl. Mawar', 'Jl. Melati', 'Jl. Anggrek', 'Jl. Dahlia', 'Jl. Kenanga', 'Jl. Flamboyan', 'Jl. Teratai'];

        $detailAddress = fake()->randomElement($streets) . ' No. ' . fake()->numberBetween(1, 500) .
            ', RT ' . str_pad((string) fake()->numberBetween(1, 20), 3, '0', STR_PAD_LEFT) .
            '/RW ' . str_pad((string) fake()->numberBetween(1, 10), 3, '0', STR_PAD_LEFT);

        // Format full address
        $fullAddress = sprintf(
            '%s, %s, %s, Kota Kendari, Sulawesi Tenggara',
            $detailAddress,
            $selectedVillage['name'],
            $selectedDistrict['district_name']
        );

        // Generate coverage area berdasarkan tipe
        $coverageArea = $type === 'resort'
            ? $this->generateResortCoverageArea($selectedDistrict, $districts)
            : $this->generatePosCoverageArea($selectedDistrict, $districts);

        // Generate coordinates (fake, untuk area Kendari)
        $coordinates = [
            'latitude' => fake()->randomFloat(6, -4.015, -3.919),
            'longitude' => fake()->randomFloat(6, 122.484, 122.633),
        ];

        return [
            'type' => $type,
            'parent_id' => null,
            'name' => ucfirst($type) . ' ' . ($type === 'resort' ? $selectedDistrict['district_name'] : $selectedVillage['name']),
            'data' => [
                'location' => [
                    'district_code' => $selectedDistrict['district_code'],
                    'district_name' => $selectedDistrict['district_name'],
                    'village_code' => $selectedVillage['code'],
                    'village_name' => $selectedVillage['name'],
                    'detail_address' => $detailAddress,
                    'address' => $fullAddress,
                    'coordinates' => $coordinates,
                ],
                'coverage_area' => $coverageArea,
                'operating_hours' => [
                    'weekday' => ['open' => '08:00', 'close' => '20:00'],
                    'weekend' => ['open' => '08:00', 'close' => '18:00'],
                ],
                'contact' => [
                    'phone' => fake()->numerify('8##########'),
                    'email' => fake()->optional()->safeEmail(),
                    'pic_name' => fake()->name(),
                ],
                'facilities' => $type === 'resort'
                    ? ['Mesin Cuci Industrial', 'Ruang Setrika', 'Ruang Packing', 'Gudang']
                    : ['Tempat Parkir', 'Ruang Tunggu'],
                'capacity' => $type === 'resort'
                    ? ['max_daily_kg' => fake()->numberBetween(500, 2000)]
                    : ['max_daily_kg' => fake()->numberBetween(100, 500)],
                'metadata' => [
                    'created_reason' => 'Factory seeding',
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
            $districts = $this->getKendariWilayah();
            $selectedDistrict = fake()->randomElement($districts);
            $selectedVillage = fake()->randomElement($selectedDistrict['villages']);

            $detailAddress = fake()->randomElement(['Jl. Ahmad Yani', 'Jl. Sudirman', 'Jl. Veteran', 'Jl. Diponegoro']) .
                ' No. ' . fake()->numberBetween(1, 200);

            $fullAddress = sprintf(
                '%s, %s, %s, Kota Kendari, Sulawesi Tenggara',
                $detailAddress,
                $selectedVillage['name'],
                $selectedDistrict['district_name']
            );

            $coverageArea = $this->generateResortCoverageArea($selectedDistrict, $districts);

            return [
                'type' => 'resort',
                'name' => 'Resort ' . $selectedDistrict['district_name'],
                'data' => [
                    'location' => [
                        'district_code' => $selectedDistrict['district_code'],
                        'district_name' => $selectedDistrict['district_name'],
                        'village_code' => $selectedVillage['code'],
                        'village_name' => $selectedVillage['name'],
                        'detail_address' => $detailAddress,
                        'address' => $fullAddress,
                        'coordinates' => [
                            'latitude' => fake()->randomFloat(6, -4.015, -3.919),
                            'longitude' => fake()->randomFloat(6, 122.484, 122.633),
                        ],
                    ],
                    'coverage_area' => $coverageArea,
                    'operating_hours' => [
                        'weekday' => ['open' => '08:00', 'close' => '20:00'],
                        'weekend' => ['open' => '08:00', 'close' => '18:00'],
                    ],
                    'contact' => [
                        'phone' => fake()->numerify('8##########'),
                        'email' => fake()->optional()->safeEmail(),
                        'pic_name' => fake()->name(),
                    ],
                    'facilities' => ['Mesin Cuci Industrial', 'Ruang Setrika', 'Ruang Packing', 'Gudang'],
                    'capacity' => ['max_daily_kg' => fake()->numberBetween(500, 2000)],
                    'metadata' => ['created_reason' => 'Factory seeding'],
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

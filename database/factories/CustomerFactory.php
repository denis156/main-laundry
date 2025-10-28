<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
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
        ];
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

        // Generate detail address
        $streets = ['Jl. Mawar', 'Jl. Melati', 'Jl. Anggrek', 'Jl. Dahlia', 'Jl. Kenanga', 'Jl. Flamboyan'];
        $detailAddress = fake()->randomElement($streets) . ' No. ' . fake()->numberBetween(1, 999) .
            ', RT ' . str_pad((string) fake()->numberBetween(1, 20), 3, '0', STR_PAD_LEFT) .
            '/RW ' . str_pad((string) fake()->numberBetween(1, 10), 3, '0', STR_PAD_LEFT);

        // Format full address
        $fullAddress = sprintf(
            '%s, %s, %s, Kota Kendari, Sulawesi Tenggara',
            $detailAddress,
            $selectedVillage['name'],
            $selectedDistrict['district_name']
        );

        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('8##########'), // Format: 81234567890 (tanpa 0 di depan)
            'email' => fake()->optional()->safeEmail(),
            'password' => 'password', // default password
            'district_code' => $selectedDistrict['district_code'],
            'district_name' => $selectedDistrict['district_name'],
            'village_code' => $selectedVillage['code'],
            'village_name' => $selectedVillage['name'],
            'detail_address' => $detailAddress,
            'address' => $fullAddress,
            'member' => fake()->boolean(40), // 40% kemungkinan member
        ];
    }
}

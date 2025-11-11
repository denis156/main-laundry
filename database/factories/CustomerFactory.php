<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Helper\WilayahHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Generate Indonesian phone number format
     */
    private function generateIndonesianPhone(): string
    {
        return fake()->numerify('8##########');
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

        // Generate detail address
        $streets = ['Jl. Mawar', 'Jl. Melati', 'Jl. Anggrek', 'Jl. Dahlia', 'Jl. Kenanga', 'Jl. Flamboyan'];
        $detailAddress = fake()->randomElement($streets) . ' No. ' . fake()->numberBetween(1, 999) .
            ', RT ' . str_pad((string) fake()->numberBetween(1, 20), 3, '0', STR_PAD_LEFT) .
            '/RW ' . str_pad((string) fake()->numberBetween(1, 10), 3, '0', STR_PAD_LEFT);

        // Format full address menggunakan WilayahHelper
        $fullAddress = WilayahHelper::formatFullAddress(
            $detailAddress,
            $selectedVillage['name'],
            $selectedDistrict['name']
        );

        // Decide login method (phone or email)
        $usePhone = fake()->boolean(70); // 70% menggunakan phone untuk login

        return [
            'email' => $usePhone ? null : fake()->unique()->safeEmail(),
            'phone' => $usePhone ? $this->generateIndonesianPhone() : null,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'data' => [
                'name' => fake()->name(),
                'avatar_url' => null,
                'addresses' => [
                    [
                        'type' => 'Rumah',
                        'district_code' => $selectedDistrict['code'],
                        'district_name' => $selectedDistrict['name'],
                        'village_code' => $selectedVillage['code'],
                        'village_name' => $selectedVillage['name'],
                        'detail_address' => $detailAddress,
                        'full_address' => $fullAddress,
                        'is_default' => true,
                    ],
                ],
                'member' => fake()->boolean(40), // 40% kemungkinan member
            ],
        ];
    }

    /**
     * Indicate that the customer is a member
     */
    public function member(): static
    {
        return $this->state(function (array $attributes) {
            $data = $attributes['data'] ?? [];
            $data['member'] = true;
            return ['data' => $data];
        });
    }

    /**
     * Indicate that the customer uses email for login
     */
    public function withEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => fake()->unique()->safeEmail(),
            'phone' => null,
        ]);
    }

    /**
     * Indicate that the customer uses phone for login
     */
    public function withPhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
            'phone' => $this->generateIndonesianPhone(),
        ]);
    }
}

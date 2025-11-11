<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Courier>
 */
class CourierFactory extends Factory
{
    /**
     * Generate Indonesian phone number format
     */
    private function generateIndonesianPhone(): string
    {
        return fake()->numerify('8##########');
    }

    /**
     * Generate Indonesian vehicle number format
     */
    private function generateIndonesianVehicleNumber(): string
    {
        $plates = ['DD', 'DT', 'DN', 'DP']; // Sulawesi Tenggara plates
        return fake()->randomElement($plates) . ' ' . fake()->numberBetween(1000, 9999) . ' ' . strtoupper(fake()->randomLetter()) . strtoupper(fake()->randomLetter());
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'assigned_location_id' => Location::factory(),
            'data' => [
                'avatar_url' => null,
                'name' => fake()->name(),
                'phone' => $this->generateIndonesianPhone(),
                'vehicle_number' => $this->generateIndonesianVehicleNumber(),
                'is_active' => fake()->boolean(85),
            ],
        ];
    }

    /**
     * Indicate that the courier is assigned to a POS location
     */
    public function assignedToPos(): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_location_id' => Location::factory()->standalone(),
        ]);
    }

    /**
     * Indicate that the courier is active
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $data = $attributes['data'] ?? [];
            $data['is_active'] = true;
            return ['data' => $data];
        });
    }

    /**
     * Indicate that the courier is inactive
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            $data = $attributes['data'] ?? [];
            $data['is_active'] = false;
            return ['data' => $data];
        });
    }
}

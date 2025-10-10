<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Cuci Kering',
                'Cuci Setrika',
                'Setrika Saja',
                'Cuci Express',
                'Cuci Premium',
                'Dry Clean',
            ]),
            'price_per_kg' => fake()->randomElement([5000, 7000, 8000, 10000, 12000, 15000]),
            'duration_days' => fake()->randomElement([1, 2, 3, 5]),
            'is_active' => fake()->boolean(95), // 95% aktif
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resort>
 */
class ResortFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $area = fake()->randomElement([
            'Kendari Barat',
            'Kendari',
            'Kambu',
            'Kadia',
            'Abeli',
            'Wua-Wua',
        ]);

        return [
            'name' => 'Resort ' . $area,
            'address' => fake()->address(),
            'phone' => fake()->numerify('08##########'),
            'pic_name' => fake()->name(),
            'is_active' => fake()->boolean(90),
        ];
    }
}

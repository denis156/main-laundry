<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Resort;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pos>
 */
class PosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $area = fake()->randomElement([
            'Purirano',
            'Benubenua',
            'Tobimeita',
            'Lalolara',
            'Mandonga',
            'Wua-Wua',
            'Poasia',
            'Baruga',
            'Kambu',
            'Korumba',
            'Lepo-Lepo',
            'Padaleu',
            'Kadia',
            'Bende',
            'Watubangga',
            'Gunung Jati',
            'Abeli',
            'Anduonohu',
            'Lapulu',
            'Tipulu',
            'Mokoau',
            'Bungkutoko',
            'Lahundape',
        ]);

        return [
            'resort_id' => null, // Default pos berdiri sendiri
            'name' => 'Pos ' . $area,
            'address' => fake()->address(),
            'phone' => fake()->numerify('08##########'),
            'pic_name' => fake()->name(),
            'area' => $area,
            'is_active' => fake()->boolean(90),
        ];
    }

    /**
     * Indicate that the pos belongs to a resort
     */
    public function withResort(): static
    {
        return $this->state(fn (array $attributes) => [
            'resort_id' => Resort::factory(),
        ]);
    }

    /**
     * Indicate that the pos is standalone (independent)
     */
    public function standalone(): static
    {
        return $this->state(fn (array $attributes) => [
            'resort_id' => null,
        ]);
    }
}

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

        $isMainPost = false; // Default resort biasa

        return [
            'name' => 'Resort ' . $area,
            'address' => fake()->address(),
            'phone' => fake()->numerify('08##########'),
            'pic_name' => fake()->name(),
            'area_coverage' => null, // Resort biasa tidak punya area coverage
            'is_active' => fake()->boolean(90),
            'is_main_post' => $isMainPost,
        ];
    }

    /**
     * Indicate that the resort is a main post (pos pusat).
     */
    public function mainPost(): static
    {
        // Area yang dilayani oleh pos pusat
        $allAreas = [
            'Purirano', 'Benubenua', 'Tobimeita', 'Lalolara', // Kendari Barat
            'Mandonga', 'Wua-Wua', 'Poasia', 'Baruga', // Kendari
            'Kambu', 'Korumba', 'Lepo-Lepo', 'Padaleu', // Kambu
            'Kadia', 'Bende', 'Watubangga', 'Gunung Jati', // Kadia
            'Abeli', 'Anduonohu', 'Lapulu', 'Tipulu', // Abeli
            'Mokoau', 'Bungkutoko', 'Lahundape', // Wua-Wua
        ];

        return $this->state(fn (array $attributes) => [
            'name' => 'Pos Pusat Laundry',
            'area_coverage' => $allAreas, // Pos pusat melayani semua area
            'is_main_post' => true,
        ]);
    }
}

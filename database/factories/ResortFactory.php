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
        $areas = [
            'Kendari Barat' => ['Purirano', 'Benubenua', 'Tobimeita', 'Lalolara'],
            'Kendari' => ['Mandonga', 'Wua-Wua', 'Poasia', 'Baruga'],
            'Kambu' => ['Kambu', 'Korumba', 'Lepo-Lepo', 'Padaleu'],
            'Kadia' => ['Kadia', 'Bende', 'Watubangga', 'Gunung Jati'],
            'Abeli' => ['Abeli', 'Anduonohu', 'Lapulu', 'Tipulu'],
            'Wua-Wua' => ['Wua-Wua', 'Mokoau', 'Bungkutoko', 'Lahundape'],
        ];

        $area = fake()->randomElement(array_keys($areas));
        $isMainPost = false; // Default resort biasa

        return [
            'name' => 'Resort ' . $area,
            'address' => fake()->address(),
            'phone' => fake()->numerify('08##########'),
            'pic_name' => fake()->name(),
            'area_coverage' => $isMainPost ? null : $areas[$area], // Jika pos pusat, area_coverage null
            'is_active' => fake()->boolean(90),
            'is_main_post' => $isMainPost,
        ];
    }

    /**
     * Indicate that the resort is a main post (pos pusat).
     */
    public function mainPost(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Pos Pusat Laundry',
            'area_coverage' => null, // Pos pusat tidak punya area coverage
            'is_main_post' => true,
        ]);
    }
}

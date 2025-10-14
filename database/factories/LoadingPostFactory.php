<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoadingPost>
 */
class LoadingPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $areas = [
            'Jakarta Selatan' => ['Kebayoran Baru', 'Tebet', 'Pancoran', 'Cilandak', 'Jagakarsa'],
            'Jakarta Pusat' => ['Menteng', 'Tanah Abang', 'Gambir', 'Cempaka Putih'],
            'Jakarta Timur' => ['Jatinegara', 'Matraman', 'Pulo Gadung', 'Cakung'],
            'Jakarta Barat' => ['Kebon Jeruk', 'Grogol Petamburan', 'Palmerah', 'Tambora'],
            'Jakarta Utara' => ['Tanjung Priok', 'Kelapa Gading', 'Pademangan', 'Penjaringan'],
        ];

        $area = fake()->randomElement(array_keys($areas));

        return [
            'name' => 'Pos Loading ' . $area,
            'address' => fake()->address(),
            'phone' => fake()->numerify('08##########'),
            'pic_name' => fake()->name(),
            'area_coverage' => $areas[$area],
            'is_active' => fake()->boolean(90),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClothingType>
 */
class ClothingTypeFactory extends Factory
{
    /**
     * Daftar jenis pakaian umum yang sering dicuci
     */
    private function getClothingTypes(): array
    {
        return [
            [
                'name' => 'Kemeja',
                'description' => 'Kemeja lengan panjang atau pendek, formal maupun kasual',
                'care_instructions' => ['Setrika suhu sedang', 'Cuci dengan air dingin'],
            ],
            [
                'name' => 'Celana Panjang',
                'description' => 'Celana panjang formal, jeans, chino, atau kasual',
                'care_instructions' => ['Hindari pemutih', 'Setrika suhu sedang'],
            ],
            [
                'name' => 'Celana Pendek',
                'description' => 'Celana pendek kasual, olahraga, atau formal',
                'care_instructions' => ['Cuci dengan air dingin', 'Jemur di tempat teduh'],
            ],
            [
                'name' => 'Kaos/T-Shirt',
                'description' => 'Kaos oblong lengan pendek atau panjang',
                'care_instructions' => ['Cuci dengan air dingin', 'Hindari setrika langsung pada gambar'],
            ],
            [
                'name' => 'Rok',
                'description' => 'Rok panjang, pendek, atau midi',
                'care_instructions' => ['Setrika suhu rendah-sedang', 'Cuci dengan pewangi lembut'],
            ],
            [
                'name' => 'Dress',
                'description' => 'Gaun atau dress formal maupun kasual',
                'care_instructions' => ['Cuci dengan hati-hati', 'Setrika suhu rendah'],
            ],
            [
                'name' => 'Jaket',
                'description' => 'Jaket tipis, tebal, atau blazer',
                'care_instructions' => ['Dry clean direkomendasikan', 'Hindari mesin pengering'],
            ],
            [
                'name' => 'Sweater/Hoodie',
                'description' => 'Sweater, hoodie, atau cardigan',
                'care_instructions' => ['Cuci dengan air dingin', 'Jangan diperas', 'Jemur mendatar'],
            ],
            [
                'name' => 'Jilbab/Kerudung',
                'description' => 'Hijab, pashmina, atau kerudung',
                'care_instructions' => ['Cuci dengan detergen lembut', 'Hindari pemutih'],
            ],
            [
                'name' => 'Mukena',
                'description' => 'Mukena untuk sholat',
                'care_instructions' => ['Cuci terpisah', 'Setrika suhu rendah'],
            ],
            [
                'name' => 'Handuk',
                'description' => 'Handuk mandi atau handuk kecil',
                'care_instructions' => ['Cuci dengan air panas', 'Boleh diputihkan'],
            ],
            [
                'name' => 'Sprei',
                'description' => 'Seprai atau bed cover',
                'care_instructions' => ['Cuci dengan air hangat', 'Setrika suhu sedang'],
            ],
            [
                'name' => 'Selimut',
                'description' => 'Selimut tipis atau tebal',
                'care_instructions' => ['Cuci dengan mesin kapasitas besar', 'Jemur hingga kering sempurna'],
            ],
            [
                'name' => 'Karpet Kecil',
                'description' => 'Karpet atau permadani ukuran kecil-sedang',
                'care_instructions' => ['Cuci khusus karpet', 'Jemur di tempat yang luas'],
            ],
            [
                'name' => 'Gordyn',
                'description' => 'Gorden atau tirai',
                'care_instructions' => ['Cuci dengan hati-hati', 'Hindari pemutih'],
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
        $clothing = fake()->randomElement($this->getClothingTypes());

        return [
            'name' => $clothing['name'],
            'data' => [
                'description' => $clothing['description'],
                'care_instructions' => $clothing['care_instructions'],
            ],
            'is_active' => fake()->boolean(95), // 95% aktif
        ];
    }

    /**
     * Indicate that the clothing type is active
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the clothing type is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $materials = [
            ['name' => 'Detergen Bubuk', 'type' => 'detergen', 'unit' => 'kg'],
            ['name' => 'Detergen Cair', 'type' => 'detergen', 'unit' => 'liter'],
            ['name' => 'Pewangi Pakaian', 'type' => 'pewangi', 'unit' => 'liter'],
            ['name' => 'Softener', 'type' => 'softener', 'unit' => 'liter'],
            ['name' => 'Pemutih Pakaian', 'type' => 'pemutih', 'unit' => 'liter'],
            ['name' => 'Plastik Kemasan Kecil', 'type' => 'plastik', 'unit' => 'pcs'],
            ['name' => 'Plastik Kemasan Besar', 'type' => 'plastik', 'unit' => 'pcs'],
            ['name' => 'Hanger', 'type' => 'aksesoris', 'unit' => 'pcs'],
        ];

        $material = fake()->randomElement($materials);
        $initialStock = fake()->randomFloat(2, 50, 500);
        $currentStock = fake()->randomFloat(2, 10, $initialStock);
        $minimumStock = $currentStock * 0.2;

        $needsExpiry = in_array($material['type'], ['detergen', 'pewangi', 'softener', 'pemutih']);

        return [
            'name' => $material['name'] . ' ' . fake()->randomElement(['A', 'B', 'Premium', 'Standard']),
            'type' => $material['type'],
            'unit' => $material['unit'],
            'initial_stock' => $initialStock,
            'current_stock' => $currentStock,
            'minimum_stock' => $minimumStock,
            'price_per_unit' => fake()->randomFloat(2, 5000, 100000),
            'expired_date' => $needsExpiry ? fake()->dateTimeBetween('now', '+2 years') : null,
            'last_updated_by' => User::query()->inRandomOrder()->first()?->id,
        ];
    }
}

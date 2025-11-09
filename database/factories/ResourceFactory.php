<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['equipment', 'material']);

        if ($type === 'equipment') {
            return $this->equipmentDefinition();
        }

        return $this->materialDefinition();
    }

    /**
     * Generate equipment resource data
     */
    private function equipmentDefinition(): array
    {
        $equipmentNames = [
            'Mesin Cuci Industrial',
            'Mesin Cuci Front Load',
            'Mesin Pengering Pakaian',
            'Setrika Uap',
            'Vacuum Cleaner',
            'Mesin Dryer',
            'Mesin Press',
            'Water Heater',
        ];

        $brands = ['Samsung', 'LG', 'Electrolux', 'Sharp', 'Panasonic', 'Polytron'];
        $status = fake()->randomElement(['baik', 'rusak', 'maintenance']);

        return [
            'type' => 'equipment',
            'name' => fake()->randomElement($equipmentNames),
            'data' => [
                'brand' => fake()->randomElement($brands),
                'serial_number' => fake()->boolean(70) ? fake()->regexify('[A-Z]{2}[0-9]{6}') : null,
                'purchase_price' => fake()->randomFloat(2, 2000000, 15000000),
                'purchase_date' => fake()->dateTimeBetween('-3 years', '-6 months')->format('Y-m-d'),
                'status' => $status,
                'maintenance' => [
                    'last_date' => fake()->boolean(60) ? fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d') : null,
                    'last_cost' => fake()->boolean(60) ? fake()->randomFloat(2, 100000, 1500000) : null,
                    'next_date' => fake()->boolean(40) ? fake()->dateTimeBetween('now', '+6 months')->format('Y-m-d') : null,
                ],
                'notes' => fake()->optional()->sentence(),
            ],
            'is_active' => $status === 'baik',
        ];
    }

    /**
     * Generate material resource data
     */
    private function materialDefinition(): array
    {
        $materials = [
            ['name' => 'Detergen Bubuk', 'unit' => 'kg'],
            ['name' => 'Detergen Cair', 'unit' => 'liter'],
            ['name' => 'Pewangi Pakaian', 'unit' => 'liter'],
            ['name' => 'Softener', 'unit' => 'liter'],
            ['name' => 'Pemutih Pakaian', 'unit' => 'liter'],
            ['name' => 'Plastik Kemasan Kecil', 'unit' => 'pcs'],
            ['name' => 'Plastik Kemasan Besar', 'unit' => 'pcs'],
            ['name' => 'Hanger', 'unit' => 'pcs'],
            ['name' => 'Kantong Plastik', 'unit' => 'pack'],
            ['name' => 'Label Stiker', 'unit' => 'box'],
        ];

        $material = fake()->randomElement($materials);
        $initialStock = fake()->randomFloat(2, 50, 500);
        $currentStock = fake()->randomFloat(2, 10, $initialStock);
        $minimumStock = $currentStock * 0.2;

        $needsExpiry = in_array($material['unit'], ['liter', 'kg']);

        return [
            'type' => 'material',
            'name' => $material['name'],
            'data' => [
                'unit' => $material['unit'],
                'stocks' => [
                    'initial' => $initialStock,
                    'current' => $currentStock,
                    'minimum' => $minimumStock,
                ],
                'pricing' => [
                    'price_per_unit' => fake()->randomFloat(2, 5000, 100000),
                    'supplier' => fake()->company(),
                ],
                'expired_date' => $needsExpiry ? fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d') : null,
                'notes' => fake()->optional()->sentence(),
            ],
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the resource is equipment
     */
    public function equipment(): static
    {
        return $this->state(fn (array $attributes) => $this->equipmentDefinition());
    }

    /**
     * Indicate that the resource is material
     */
    public function material(): static
    {
        return $this->state(fn (array $attributes) => $this->materialDefinition());
    }
}

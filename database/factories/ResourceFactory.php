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
        $equipmentTypes = [
            'Mesin Cuci',
            'Setrika',
            'Pengering',
            'Dryer',
            'Vacuum Cleaner',
        ];

        $brands = [
            'Samsung',
            'LG',
            'Electrolux',
            'Sharp',
            'Panasonic',
            'Polytron',
            'Denpoo',
        ];

        $status = fake()->randomElement(['baik', 'rusak', 'maintenance']);
        $hasMaintenanceHistory = fake()->boolean(60);

        $equipmentName = fake()->randomElement($equipmentTypes) . ' ' . fake()->randomElement(['Pro', 'Plus', 'Deluxe', 'Standard', 'Premium']);

        return [
            'type' => 'equipment',
            'name' => $equipmentName,
            'data' => [
                'equipment_type' => fake()->randomElement($equipmentTypes),
                'brand' => fake()->randomElement($brands),
                'serial_number' => fake()->boolean(70) ? fake()->regexify('[A-Z]{2}[0-9]{6}') : null,
                'purchase_price' => fake()->randomFloat(2, 2000000, 15000000),
                'purchase_date' => fake()->dateTimeBetween('-3 years', '-6 months')->format('Y-m-d'),
                'status' => $status,
                'last_maintenance_date' => $hasMaintenanceHistory ? fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d') : null,
                'last_maintenance_cost' => $hasMaintenanceHistory ? fake()->randomFloat(2, 100000, 1500000) : null,
                'maintenance_history' => $hasMaintenanceHistory ? [
                    [
                        'date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                        'cost' => fake()->randomFloat(2, 100000, 1500000),
                        'description' => fake()->randomElement([
                            'Penggantian spare part',
                            'Pembersihan menyeluruh',
                            'Perbaikan mesin',
                            'Servis rutin',
                        ]),
                        'performed_by' => fake()->randomElement(['Teknisi A', 'Teknisi B', 'Service Center Resmi']),
                    ],
                ] : [],
                'certifications' => fake()->boolean(40) ? ['SNI', 'ISO 9001'] : [],
                'storage' => [
                    'location' => fake()->randomElement(['Gudang A', 'Gudang B', 'Ruang Produksi']),
                    'section' => fake()->randomElement(['A1', 'A2', 'B1', 'B2']),
                ],
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

        $materialName = $material['name'] . ' ' . fake()->randomElement(['A', 'B', 'Premium', 'Standard']);

        return [
            'type' => 'material',
            'name' => $materialName,
            'data' => [
                'material_type' => $material['type'],
                'unit' => $material['unit'],
                'stocks' => [
                    'initial_stock' => $initialStock,
                    'current_stock' => $currentStock,
                    'minimum_stock' => $minimumStock,
                ],
                'pricing' => [
                    'price_per_unit' => fake()->randomFloat(2, 5000, 100000),
                    'currency' => 'IDR',
                ],
                'supplier' => [
                    'name' => fake()->company(),
                    'contact' => fake()->numerify('8##########'),
                    'email' => fake()->safeEmail(),
                ],
                'expired_date' => $needsExpiry ? fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d') : null,
                'usage_rate' => [
                    'per_kg_laundry' => fake()->randomFloat(2, 0.05, 0.5),
                    'unit' => $material['unit'],
                ],
                'storage' => [
                    'location' => fake()->randomElement(['Gudang Material', 'Ruang Penyimpanan A', 'Ruang Penyimpanan B']),
                    'section' => fake()->randomElement(['A1', 'A2', 'B1', 'B2']),
                ],
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

<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
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

        $statuses = ['baik', 'rusak', 'maintenance'];
        $status = fake()->randomElement($statuses);

        $hasMaintenanceHistory = fake()->boolean(60);

        return [
            'name' => fake()->randomElement($equipmentTypes) . ' ' . fake()->randomElement(['Pro', 'Plus', 'Deluxe', 'Standard', 'Premium']),
            'type' => fake()->randomElement($equipmentTypes),
            'brand' => fake()->randomElement($brands),
            'serial_number' => fake()->boolean(70) ? fake()->regexify('[A-Z]{2}[0-9]{6}') : null,
            'purchase_price' => fake()->randomFloat(2, 2000000, 15000000),
            'purchase_date' => fake()->dateTimeBetween('-3 years', '-6 months'),
            'status' => $status,
            'last_maintenance_date' => $hasMaintenanceHistory ? fake()->dateTimeBetween('-6 months', 'now') : null,
            'last_maintenance_cost' => $hasMaintenanceHistory ? fake()->randomFloat(2, 100000, 1500000) : null,
        ];
    }
}

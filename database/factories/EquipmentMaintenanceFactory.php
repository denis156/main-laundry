<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipmentMaintenance>
 */
class EquipmentMaintenanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $descriptions = [
            'Penggantian spare part',
            'Pembersihan menyeluruh',
            'Perbaikan mesin',
            'Servis rutin',
            'Penggantian oli',
            'Kalibrasi mesin',
            'Perbaikan kerusakan',
        ];

        $performers = [
            'Teknisi A',
            'Teknisi B',
            'Service Center Resmi',
            'Tim Maintenance Internal',
            null,
        ];

        return [
            'equipment_id' => Equipment::factory(),
            'maintenance_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'cost' => fake()->randomFloat(2, 100000, 2000000),
            'description' => fake()->randomElement($descriptions) . ' - ' . fake()->sentence(),
            'performed_by' => fake()->randomElement($performers),
        ];
    }
}

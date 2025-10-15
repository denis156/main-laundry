<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use App\Models\Resort;
use App\Models\Pos;
use App\Models\CourierMotorcycle;
use App\Models\CourierCarSchedule;
use App\Models\Equipment;
use App\Models\EquipmentMaintenance;
use App\Models\Material;
use App\Models\MaterialStockHistory;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed data master seperti users, services, resorts, dan couriers
     */
    public function run(): void
    {
        // Buat super admin user
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@laundry.com',
        ]);

        // Buat staff users
        User::factory()->count(5)->create();

        // Buat services
        Service::factory()->create([
            'name' => 'Cuci Kering',
            'price_per_kg' => 5000,
            'duration_days' => 3,
        ]);

        Service::factory()->create([
            'name' => 'Cuci Setrika',
            'price_per_kg' => 7000,
            'duration_days' => 3,
        ]);

        Service::factory()->create([
            'name' => 'Setrika Saja',
            'price_per_kg' => 4000,
            'duration_days' => 2,
        ]);

        Service::factory()->create([
            'name' => 'Cuci Express',
            'price_per_kg' => 10000,
            'duration_days' => 1,
        ]);

        Service::factory()->create([
            'name' => 'Cuci Premium',
            'price_per_kg' => 12000,
            'duration_days' => 3,
        ]);

        Service::factory()->create([
            'name' => 'Dry Clean',
            'price_per_kg' => 15000,
            'duration_days' => 5,
        ]);

        // Buat resorts (6 resort untuk Kendari)
        $areas = [
            'Kendari Barat',
            'Kendari',
            'Kambu',
            'Kadia',
            'Abeli',
            'Wua-Wua',
        ];

        $resorts = [];
        foreach ($areas as $area) {
            $resort = Resort::factory()->create([
                'name' => 'Resort ' . $area,
            ]);
            $resorts[] = $resort;

            // Buat 2-3 pos untuk setiap resort
            Pos::factory()->count(fake()->numberBetween(2, 3))->create([
                'resort_id' => $resort->id,
            ]);
        }

        // Buat beberapa pos berdiri sendiri (standalone)
        Pos::factory()->standalone()->count(5)->create();

        // Ambil semua pos untuk assign courier
        $allPos = Pos::all();

        // Buat courier motorcycle (2-3 kurir per pos)
        foreach ($allPos as $pos) {
            CourierMotorcycle::factory()->count(fake()->numberBetween(1, 2))->create([
                'assigned_pos_id' => $pos->id,
            ]);
        }

        // Buat beberapa courier car schedules
        CourierCarSchedule::factory()->count(10)->create();

        // Buat equipments (alat-alat)
        $equipments = Equipment::factory()->count(15)->create();

        // Buat equipment maintenance history untuk beberapa alat
        foreach ($equipments->random(10) as $equipment) {
            EquipmentMaintenance::factory()->count(fake()->numberBetween(1, 5))->create([
                'equipment_id' => $equipment->id,
            ]);
        }

        // Buat materials (bahan-bahan)
        $materials = Material::factory()->count(20)->create();

        // Buat material stock history untuk setiap bahan
        foreach ($materials as $material) {
            MaterialStockHistory::factory()->count(fake()->numberBetween(3, 10))->create([
                'material_id' => $material->id,
            ]);
        }
    }
}

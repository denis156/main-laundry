<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed data master seperti users, services, dan promos
     */
    public function run(): void
    {
        // Buat super admin user
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@laundry.com',
        ]);

        // Buat staff users
        User::factory()->count(13)->create();

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

        // Buat promos
        Promo::factory()->count(10)->create();
    }
}

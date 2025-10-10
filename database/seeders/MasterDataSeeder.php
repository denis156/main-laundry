<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MembershipTier;
use App\Models\Promo;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed data master seperti users, membership tiers, services, dan promos
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

        // Buat membership tiers dengan urutan yang benar
        MembershipTier::factory()->create([
            'name' => 'Bronze',
            'slug' => 'bronze',
            'min_points' => 0,
            'discount_percentage' => 5,
            'sort_order' => 1,
        ]);

        MembershipTier::factory()->create([
            'name' => 'Silver',
            'slug' => 'silver',
            'min_points' => 500,
            'discount_percentage' => 10,
            'sort_order' => 2,
        ]);

        MembershipTier::factory()->create([
            'name' => 'Gold',
            'slug' => 'gold',
            'min_points' => 1500,
            'discount_percentage' => 15,
            'sort_order' => 3,
        ]);

        MembershipTier::factory()->create([
            'name' => 'Platinum',
            'slug' => 'platinum',
            'min_points' => 3000,
            'discount_percentage' => 20,
            'sort_order' => 4,
        ]);

        MembershipTier::factory()->create([
            'name' => 'Diamond',
            'slug' => 'diamond',
            'min_points' => 5000,
            'discount_percentage' => 25,
            'sort_order' => 5,
        ]);

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

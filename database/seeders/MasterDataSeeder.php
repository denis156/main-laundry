<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use App\Models\LoadingPost;
use App\Models\CourierMotorcycle;
use App\Models\CourierCarSchedule;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed data master seperti users, services, loading posts, dan couriers
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

        // Buat loading posts (5 pos untuk Jakarta)
        $posts = [
            'Jakarta Selatan',
            'Jakarta Pusat',
            'Jakarta Timur',
            'Jakarta Barat',
            'Jakarta Utara',
        ];

        $loadingPosts = [];
        foreach ($posts as $area) {
            $loadingPosts[] = LoadingPost::factory()->create([
                'name' => 'Pos Loading ' . $area,
            ]);
        }

        // Buat courier motorcycle (2-3 kurir per pos)
        foreach ($loadingPosts as $post) {
            CourierMotorcycle::factory()->count(fake()->numberBetween(2, 3))->create([
                'assigned_loading_post_id' => $post->id,
            ]);
        }

        // Buat beberapa courier car schedules
        CourierCarSchedule::factory()->count(10)->create();
    }
}

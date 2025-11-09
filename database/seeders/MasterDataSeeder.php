<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use App\Models\Location;
use App\Models\Courier;
use App\Models\CourierCarSchedule;
use App\Models\Resource;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed data master seperti users, services, locations, dan couriers
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

        // Buat services dengan data JSONB yang lengkap
        $servicesData = [
            [
                'name' => 'Cuci Kering',
                'service_type' => 'cuci_kering',
                'price_per_kg' => 5000,
                'duration_days' => 2,
                'features' => ['Pencucian bersih', 'Pengeringan maksimal'],
                'icon' => 'washing-machine',
                'color' => '#3B82F6',
                'is_featured' => false,
            ],
            [
                'name' => 'Cuci Setrika',
                'service_type' => 'cuci_setrika',
                'price_per_kg' => 7000,
                'duration_days' => 3,
                'features' => ['Pencucian bersih', 'Pengeringan', 'Setrika rapi', 'Lipat & Packing'],
                'icon' => 'iron',
                'color' => '#10B981',
                'is_featured' => true,
            ],
            [
                'name' => 'Setrika Saja',
                'service_type' => 'setrika',
                'price_per_kg' => 4000,
                'duration_days' => 1,
                'features' => ['Setrika rapi', 'Lipat & Packing'],
                'icon' => 'shirt',
                'color' => '#F59E0B',
                'is_featured' => false,
            ],
            [
                'name' => 'Cuci Express',
                'service_type' => 'cuci_express',
                'price_per_kg' => 10000,
                'duration_days' => 1,
                'features' => ['Pencucian cepat', 'Pengeringan', 'Setrika', 'Selesai 24 jam'],
                'icon' => 'zap',
                'color' => '#EF4444',
                'is_featured' => true,
            ],
            [
                'name' => 'Cuci Premium',
                'service_type' => 'cuci_premium',
                'price_per_kg' => 12000,
                'duration_days' => 3,
                'features' => ['Detergen premium', 'Pewangi khusus', 'Setrika premium', 'Packing eksklusif'],
                'icon' => 'star',
                'color' => '#8B5CF6',
                'is_featured' => false,
            ],
            [
                'name' => 'Dry Clean',
                'service_type' => 'dry_clean',
                'price_per_kg' => 15000,
                'duration_days' => 5,
                'features' => ['Pembersihan kering', 'Untuk bahan sensitif', 'Treatment khusus', 'Setrika profesional'],
                'icon' => 'droplet',
                'color' => '#06B6D4',
                'is_featured' => false,
            ],
        ];

        foreach ($servicesData as $serviceData) {
            Service::create([
                'name' => $serviceData['name'],
                'is_featured' => $serviceData['is_featured'],
                'sort_order' => 0,
                'data' => [
                    'service_type' => $serviceData['service_type'],
                    'pricing' => [
                        'price_per_kg' => $serviceData['price_per_kg'],
                        'currency' => 'IDR',
                    ],
                    'pricing_tiers' => [
                        ['min_kg' => 0, 'max_kg' => 5, 'price_per_kg' => $serviceData['price_per_kg']],
                        ['min_kg' => 5, 'max_kg' => 10, 'price_per_kg' => $serviceData['price_per_kg'] * 0.95],
                        ['min_kg' => 10, 'max_kg' => null, 'price_per_kg' => $serviceData['price_per_kg'] * 0.90],
                    ],
                    'duration_days' => $serviceData['duration_days'],
                    'features' => $serviceData['features'],
                    'includes' => [
                        'Detergen berkualitas',
                        'Pewangi pakaian',
                        'Packing plastik',
                    ],
                    'restrictions' => [
                        'Tidak menerima bahan kulit',
                        'Tidak menerima sepatu',
                    ],
                    'materials_used' => [
                        'Detergen premium',
                        'Softener',
                        'Pewangi',
                    ],
                    'icon' => $serviceData['icon'],
                    'color' => $serviceData['color'],
                    'badge_settings' => $serviceData['is_featured'] ? [
                        'text' => 'Recommended',
                        'color' => '#F59E0B',
                    ] : null,
                ],
                'is_active' => true,
            ]);
        }

        // Buat resorts (6 resort untuk Kendari)
        $resorts = Location::factory()->resort()->count(6)->create();

        // Buat 2-3 pos untuk setiap resort
        foreach ($resorts as $resort) {
            Location::factory()->count(fake()->numberBetween(2, 3))->create([
                'type' => 'pos',
                'parent_id' => $resort->id,
            ]);
        }

        // Buat beberapa pos berdiri sendiri (standalone)
        Location::factory()->standalone()->count(5)->create();

        // Ambil semua pos untuk assign courier
        $allPosLocations = Location::where('type', 'pos')->get();

        // Buat courier (2-3 kurir per pos)
        foreach ($allPosLocations as $pos) {
            Courier::factory()->count(fake()->numberBetween(1, 2))->create([
                'assigned_location_id' => $pos->id,
            ]);
        }

        // Buat beberapa courier car schedules
        CourierCarSchedule::factory()->count(10)->create();

        // Buat resources (equipment & materials)
        Resource::factory()->equipment()->count(15)->create();
        Resource::factory()->material()->count(20)->create();
    }
}

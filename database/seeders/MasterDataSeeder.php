<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use App\Models\Location;
use App\Models\Courier;
use App\Models\CourierCarSchedule;
use App\Models\Resource;
use App\Models\ClothingType;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed data master seperti users, services, clothing types, locations, dan couriers
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

        // Seed clothing types (jenis pakaian)
        $this->call(ClothingTypeSeeder::class);

        // Buat services dengan data JSONB yang lengkap
        $servicesData = [
            [
                'name' => 'Cuci Kering',
                'service_type' => 'cuci-kering',
                'unit' => 'per_kg',
                'price_per_kg' => 5000,
                'price_per_item' => null,
                'duration_hours' => 48, // 2 hari
                'features' => ['Pencucian bersih', 'Pengeringan maksimal'],
                'icon' => 'washing-machine',
                'color' => '#3B82F6',
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Cuci Setrika',
                'service_type' => 'cuci-setrika',
                'unit' => 'per_kg',
                'price_per_kg' => 7000,
                'price_per_item' => null,
                'duration_hours' => 72, // 3 hari
                'features' => ['Pencucian bersih', 'Pengeringan', 'Setrika rapi', 'Lipat & Packing'],
                'icon' => 'iron',
                'color' => '#10B981',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Setrika Saja',
                'service_type' => 'setrika',
                'unit' => 'per_kg',
                'price_per_kg' => 4000,
                'price_per_item' => null,
                'duration_hours' => 24, // 1 hari
                'features' => ['Setrika rapi', 'Lipat & Packing'],
                'icon' => 'shirt',
                'color' => '#F59E0B',
                'is_featured' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Cuci Express',
                'service_type' => 'cuci-express',
                'unit' => 'per_kg',
                'price_per_kg' => 10000,
                'price_per_item' => null,
                'duration_hours' => 24, // 1 hari
                'features' => ['Pencucian cepat', 'Pengeringan', 'Setrika', 'Selesai 24 jam'],
                'icon' => 'zap',
                'color' => '#EF4444',
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Cuci Premium',
                'service_type' => 'cuci-premium',
                'unit' => 'per_kg',
                'price_per_kg' => 12000,
                'price_per_item' => null,
                'duration_hours' => 72, // 3 hari
                'features' => ['Detergen premium', 'Pewangi khusus', 'Setrika premium', 'Packing eksklusif'],
                'icon' => 'star',
                'color' => '#8B5CF6',
                'is_featured' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'Dry Clean',
                'service_type' => 'dry-clean',
                'unit' => 'per_kg',
                'price_per_kg' => 15000,
                'price_per_item' => null,
                'duration_hours' => 120, // 5 hari
                'features' => ['Pembersihan kering', 'Untuk bahan sensitif', 'Treatment khusus', 'Setrika profesional'],
                'icon' => 'droplet',
                'color' => '#06B6D4',
                'is_featured' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'Cuci Karpet Besar',
                'service_type' => 'cuci-karpet',
                'unit' => 'per_item',
                'price_per_kg' => null,
                'price_per_item' => 50000,
                'duration_hours' => 72, // 3 hari
                'features' => ['Cuci khusus karpet', 'Pengeringan sempurna', 'Vakum debu'],
                'icon' => 'droplet',
                'color' => '#EC4899',
                'is_featured' => false,
                'sort_order' => 7,
            ],
            [
                'name' => 'Cuci Selimut Tebal',
                'service_type' => 'cuci-selimut',
                'unit' => 'per_item',
                'price_per_kg' => null,
                'price_per_item' => 35000,
                'duration_hours' => 48, // 2 hari
                'features' => ['Cuci dengan mesin kapasitas besar', 'Pengeringan maksimal'],
                'icon' => 'droplet',
                'color' => '#14B8A6',
                'is_featured' => false,
                'sort_order' => 8,
            ],
        ];

        foreach ($servicesData as $serviceData) {
            Service::create([
                'name' => $serviceData['name'],
                'is_featured' => $serviceData['is_featured'],
                'sort_order' => $serviceData['sort_order'],
                'data' => [
                    'service_type' => $serviceData['service_type'],
                    'pricing' => [
                        'unit' => $serviceData['unit'],
                        'price_per_kg' => $serviceData['price_per_kg'],
                        'price_per_item' => $serviceData['price_per_item'],
                        'currency' => 'IDR',
                    ],
                    'pricing_tiers' => $serviceData['unit'] === 'per_kg' ? [
                        ['min_kg' => 0, 'max_kg' => 5, 'price_per_kg' => $serviceData['price_per_kg']],
                        ['min_kg' => 5, 'max_kg' => 10, 'price_per_kg' => $serviceData['price_per_kg'] * 0.95],
                        ['min_kg' => 10, 'max_kg' => null, 'price_per_kg' => $serviceData['price_per_kg'] * 0.90],
                    ] : [],
                    'duration_hours' => $serviceData['duration_hours'],
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

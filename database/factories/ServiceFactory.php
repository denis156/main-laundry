<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $services = [
            [
                'name' => 'Cuci Kering',
                'service_type' => 'cuci_kering',
                'price_per_kg' => 5000,
                'duration_days' => 2,
                'features' => ['Pencucian bersih', 'Pengeringan maksimal'],
                'icon' => 'washing-machine',
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Cuci Setrika',
                'service_type' => 'cuci_setrika',
                'price_per_kg' => 7000,
                'duration_days' => 3,
                'features' => ['Pencucian bersih', 'Pengeringan', 'Setrika rapi', 'Lipat & Packing'],
                'icon' => 'iron',
                'color' => '#10B981',
            ],
            [
                'name' => 'Setrika Saja',
                'service_type' => 'setrika',
                'price_per_kg' => 4000,
                'duration_days' => 1,
                'features' => ['Setrika rapi', 'Lipat & Packing'],
                'icon' => 'shirt',
                'color' => '#F59E0B',
            ],
            [
                'name' => 'Cuci Express',
                'service_type' => 'cuci_express',
                'price_per_kg' => 10000,
                'duration_days' => 1,
                'features' => ['Pencucian cepat', 'Pengeringan', 'Setrika', 'Selesai 24 jam'],
                'icon' => 'zap',
                'color' => '#EF4444',
            ],
            [
                'name' => 'Cuci Premium',
                'service_type' => 'cuci_premium',
                'price_per_kg' => 12000,
                'duration_days' => 3,
                'features' => ['Detergen premium', 'Pewangi khusus', 'Setrika premium', 'Packing eksklusif'],
                'icon' => 'star',
                'color' => '#8B5CF6',
            ],
            [
                'name' => 'Dry Clean',
                'service_type' => 'dry_clean',
                'price_per_kg' => 15000,
                'duration_days' => 5,
                'features' => ['Pembersihan kering', 'Untuk bahan sensitif', 'Treatment khusus', 'Setrika profesional'],
                'icon' => 'droplet',
                'color' => '#06B6D4',
            ],
        ];

        $selectedService = fake()->randomElement($services);
        $isFeatured = fake()->boolean(30); // 30% featured

        return [
            'name' => $selectedService['name'],
            'is_featured' => $isFeatured,
            'sort_order' => fake()->numberBetween(0, 100),
            'data' => [
                'service_type' => $selectedService['service_type'],
                'pricing' => [
                    'price_per_kg' => $selectedService['price_per_kg'],
                    'currency' => 'IDR',
                ],
                'pricing_tiers' => [
                    ['min_kg' => 0, 'max_kg' => 5, 'price_per_kg' => $selectedService['price_per_kg']],
                    ['min_kg' => 5, 'max_kg' => 10, 'price_per_kg' => $selectedService['price_per_kg'] * 0.95],
                    ['min_kg' => 10, 'max_kg' => null, 'price_per_kg' => $selectedService['price_per_kg'] * 0.90],
                ],
                'duration_days' => $selectedService['duration_days'],
                'features' => $selectedService['features'],
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
                'icon' => $selectedService['icon'],
                'color' => $selectedService['color'],
                'badge_settings' => $isFeatured ? [
                    'text' => 'Recommended',
                    'color' => '#F59E0B',
                ] : null,
            ],
            'is_active' => fake()->boolean(95), // 95% aktif
        ];
    }

    /**
     * Indicate that the service is featured
     */
    public function featured(): static
    {
        return $this->state(function (array $attributes) {
            $data = $attributes['data'] ?? [];
            $data['badge_settings'] = [
                'text' => 'Recommended',
                'color' => '#F59E0B',
            ];
            return [
                'is_featured' => true,
                'data' => $data,
            ];
        });
    }
}

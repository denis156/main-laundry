<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Helper\database\CustomerHelper;
use App\Helper\database\ServiceHelper;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Courier;
use App\Models\Location;
use App\Models\ClothingType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderDate = fake()->dateTimeBetween('-3 months', 'now');

        $workflowStatus = fake()->randomElement([
            'pending_confirmation',
            'confirmed',
            'picked_up',
            'at_loading_post',
            'in_washing',
            'washing_completed',
            'out_for_delivery',
            'delivered',
            'cancelled',
        ]);

        $paymentTiming = fake()->randomElement(['on_pickup', 'on_delivery']);
        $isPaid = fake()->boolean(60);

        $formLoadedAt = (clone $orderDate)->modify('-' . fake()->numberBetween(5, 300) . ' seconds');

        // Generate customer untuk mendapatkan address
        $customer = Customer::factory()->make();
        $customerAddress = CustomerHelper::getDefaultAddress($customer);

        // Generate service items (bisa per_kg atau per_item)
        $items = $this->generateServiceItems();
        $totalPrice = array_sum(array_column($items, 'subtotal'));

        // Generate timeline berdasarkan workflow status
        $timeline = $this->generateTimeline($workflowStatus, $orderDate);

        return [
            'invoice_number' => 'INV/' . $orderDate->format('Ymd') . '/' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => Customer::factory(),
            'courier_id' => fake()->optional(0.8)->randomElement([null, Courier::factory()]),
            'location_id' => fake()->optional(0.7)->randomElement([null, Location::factory()->standalone()]),
            'workflow_status' => $workflowStatus,
            'payment_status' => $isPaid ? 'paid' : 'unpaid',
            'data' => [
                'items' => $items,
                'pricing' => [
                    'total_price' => $totalPrice,
                    'payment_timing' => $paymentTiming,
                ],
                'customer_address' => $customerAddress ?? [
                    'district_code' => '74.71.01',
                    'district_name' => 'Mandonga',
                    'village_code' => '74.71.01.1001',
                    'village_name' => 'Mandonga',
                    'detail_address' => 'Jl. Example No. 123',
                    'full_address' => 'Jl. Example No. 123, Mandonga, Mandonga, Kota Kendari',
                ],
                'notes' => fake()->optional()->sentence(),
                'metadata' => [
                    'tags' => [],
                    'custom_fields' => [],
                ],
                'tracking' => [
                    'tracking_token' => fake()->uuid(),
                    'tracking_url' => url('/tracking/' . fake()->uuid()),
                ],
                'timeline' => $timeline,
                'anti_bot' => [
                    'customer_ip' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'form_loaded_at' => $formLoadedAt->format('Y-m-d H:i:s'),
                ],
            ],
        ];
    }

    /**
     * Generate service items (1-3 items) with clothing types support
     */
    private function generateServiceItems(): array
    {
        $items = [];
        $itemCount = fake()->numberBetween(1, 3);

        for ($i = 0; $i < $itemCount; $i++) {
            // Pilih service secara random
            $pricingUnit = fake()->randomElement(['per_kg', 'per_item']);

            if ($pricingUnit === 'per_kg') {
                $items[] = $this->generatePerKgItem();
            } else {
                $items[] = $this->generatePerItemItem();
            }
        }

        return $items;
    }

    /**
     * Generate item untuk service per_kg (dengan clothing types)
     */
    private function generatePerKgItem(): array
    {
        $pricePerKg = fake()->randomFloat(2, 5000, 15000);
        $totalWeight = fake()->randomFloat(2, 1, 20);

        // Generate clothing items (2-5 jenis pakaian)
        $clothingItems = [];
        $clothingCount = fake()->numberBetween(2, 5);

        for ($i = 0; $i < $clothingCount; $i++) {
            $clothingItems[] = [
                'clothing_type_id' => null, // Will be filled by seeder
                'clothing_type_name' => fake()->randomElement(['Kemeja', 'Celana Panjang', 'Kaos', 'Rok', 'Dress']),
                'quantity' => fake()->numberBetween(1, 10),
            ];
        }

        return [
            'service_id' => null, // Will be filled by seeder
            'service_name' => fake()->randomElement(['Cuci Kering', 'Cuci Setrika', 'Setrika Saja', 'Cuci Express', 'Cuci Premium', 'Dry Clean']),
            'pricing_unit' => 'per_kg',
            'price_per_kg' => $pricePerKg,
            'price_per_item' => null,
            'clothing_items' => $clothingItems,
            'total_weight' => $totalWeight,
            'quantity' => null,
            'subtotal' => $pricePerKg * $totalWeight,
        ];
    }

    /**
     * Generate item untuk service per_item (tanpa clothing types)
     */
    private function generatePerItemItem(): array
    {
        $pricePerItem = fake()->randomFloat(2, 30000, 60000);
        $quantity = fake()->numberBetween(1, 5);

        return [
            'service_id' => null, // Will be filled by seeder
            'service_name' => fake()->randomElement(['Cuci Karpet Besar', 'Cuci Selimut Tebal']),
            'pricing_unit' => 'per_item',
            'price_per_kg' => null,
            'price_per_item' => $pricePerItem,
            'clothing_items' => [],
            'total_weight' => null,
            'quantity' => $quantity,
            'subtotal' => $pricePerItem * $quantity,
        ];
    }

    /**
     * Generate timeline based on workflow status
     */
    private function generateTimeline(string $workflowStatus, $orderDate): array
    {
        $timeline = [];
        $currentDate = clone $orderDate;

        $statusFlow = [
            'pending_confirmation',
            'confirmed',
            'picked_up',
            'at_loading_post',
            'in_washing',
            'washing_completed',
            'out_for_delivery',
            'delivered',
        ];

        $statusIndex = array_search($workflowStatus, $statusFlow);
        if ($statusIndex === false) {
            $statusIndex = 0;
        }

        for ($i = 0; $i <= $statusIndex; $i++) {
            $timeline[] = [
                'status' => $statusFlow[$i],
                'timestamp' => $currentDate->format('Y-m-d H:i:s'),
                'notes' => fake()->optional()->sentence(),
            ];

            // Add random hours for next status
            $currentDate->modify('+' . fake()->numberBetween(1, 24) . ' hours');
        }

        return $timeline;
    }
}

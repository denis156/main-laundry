<?php

declare(strict_types=1);

namespace Database\Factories;

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

        // Generate service items (bisa per_kg atau per_item)
        $items = $this->generateServiceItems();
        $totalPrice = array_sum(array_column($items, 'subtotal'));

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
                'notes' => fake()->optional()->sentence(),
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

        // Generate clothing items (2-5 jenis pakaian) dengan proper clothing type relationships
        $clothingItems = [];
        $clothingCount = fake()->numberBetween(2, 5);

        // Get actual clothing types from database or use fallback
        $clothingTypes = ClothingType::where('is_active', true)->pluck('name', 'id')->toArray();
        if (empty($clothingTypes)) {
            $clothingTypes = ['Kemeja', 'Celana Panjang', 'Kaos', 'Rok', 'Dress'];
        }

        for ($i = 0; $i < $clothingCount; $i++) {
            if (is_numeric(array_key_first($clothingTypes))) {
                // Use actual database clothing types
                $clothingTypeId = array_rand($clothingTypes);
                $clothingTypeName = $clothingTypes[$clothingTypeId];
            } else {
                // Use fallback names
                $clothingTypeName = array_rand($clothingTypes);
                $clothingTypeId = null;
            }

            $clothingItems[] = [
                'clothing_type_id' => $clothingTypeId,
                'clothing_type_name' => $clothingTypeName,
                'quantity' => fake()->numberBetween(1, 10),
            ];
        }

        // Get actual service or use fallback
        $services = Service::where('is_active', true)->where('data->pricing->unit', 'per_kg')->pluck('name', 'id')->toArray();
        if (is_numeric(array_key_first($services))) {
            $serviceId = array_rand($services);
            $serviceName = $services[$serviceId];
        } else {
            $serviceName = fake()->randomElement(['Cuci Kering', 'Cuci Setrika', 'Setrika Saja', 'Cuci Express', 'Cuci Premium']);
            $serviceId = null;
        }

        return [
            'service_id' => $serviceId,
            'service_name' => $serviceName,
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

        // Get actual service or use fallback
        $services = Service::where('is_active', true)->where('data->pricing->unit', 'per_item')->pluck('name', 'id')->toArray();
        if (is_numeric(array_key_first($services))) {
            $serviceId = array_rand($services);
            $serviceName = $services[$serviceId];
        } else {
            $serviceName = fake()->randomElement(['Cuci Karpet Besar', 'Cuci Selimut Tebal', 'Cuci Karpet Kecil', 'Cuci Jas', 'Cuci Boneka Besar']);
            $serviceId = null;
        }

        return [
            'service_id' => $serviceId,
            'service_name' => $serviceName,
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
     * Indicate that the transaction is unpaid (for payment generation)
     */
    public function unpaid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'unpaid',
                'workflow_status' => fake()->randomElement([
                    'pending_confirmation',
                    'confirmed',
                    'picked_up',
                    'at_loading_post',
                    'in_washing',
                    'washing_completed',
                    'out_for_delivery',
                ]),
            ];
        });
    }

}

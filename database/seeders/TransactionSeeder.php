<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Helper\database\CustomerHelper;
use App\Helper\database\ServiceHelper;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\Courier;
use App\Models\Location;
use App\Models\ClothingType;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Seed data transactions dengan payments (support clothing types)
     */
    public function run(): void
    {
        $customers = Customer::all();
        $services = Service::all();
        $servicesPerKg = $services->filter(fn($s) => $s->data['pricing']['unit'] === 'per_kg');
        $servicesPerItem = $services->filter(fn($s) => $s->data['pricing']['unit'] === 'per_item');
        $couriers = Courier::all();
        $locations = Location::where('type', 'pos')->get();
        $clothingTypes = ClothingType::where('is_active', true)->get();

        // Buat 100 transaksi
        for ($i = 0; $i < 100; $i++) {
            $customer = $customers->random();
            $courier = $couriers->random();
            $selectedLocation = $locations->random();

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
            $isPaid = fake()->boolean(70);

            // Get customer address
            $customerAddress = CustomerHelper::getDefaultAddress($customer);
            $formLoadedAt = (clone $orderDate)->modify('-' . fake()->numberBetween(5, 300) . ' seconds');

            // Generate timeline
            $timeline = $this->generateTimeline($workflowStatus, $orderDate);

            // Generate items (1-3 items per transaksi)
            $items = [];
            $itemCount = fake()->numberBetween(1, 3);

            for ($j = 0; $j < $itemCount; $j++) {
                $pricingUnit = fake()->randomElement(['per_kg', 'per_item']);

                if ($pricingUnit === 'per_kg' && $servicesPerKg->isNotEmpty()) {
                    $service = $servicesPerKg->random();
                    $items[] = $this->generatePerKgItem($service, $clothingTypes);
                } elseif ($servicesPerItem->isNotEmpty()) {
                    $service = $servicesPerItem->random();
                    $items[] = $this->generatePerItemItem($service);
                }
            }

            $totalPrice = array_sum(array_column($items, 'subtotal'));

            // Buat transaction dengan struktur JSONB
            $transaction = Transaction::create([
                'invoice_number' => 'INV/' . $orderDate->format('Ymd') . '/' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'courier_id' => $courier->id,
                'location_id' => $selectedLocation->id,
                'workflow_status' => $workflowStatus,
                'payment_status' => $isPaid ? 'paid' : 'unpaid',
                'data' => [
                    'items' => $items,
                    'pricing' => [
                        'total_price' => $totalPrice,
                        'payment_timing' => $paymentTiming,
                    ],
                    'customer_address' => $customerAddress,
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
            ]);

            // Buat payment jika sudah dibayar
            if ($isPaid) {
                Payment::create([
                    'transaction_id' => $transaction->id,
                    'courier_id' => $courier->id,
                    'amount' => $totalPrice,
                    'data' => [
                        'payment_date' => fake()->dateTimeBetween($orderDate, 'now')->format('Y-m-d H:i:s'),
                        'method' => fake()->randomElement(['cash', 'transfer', 'qris']),
                        'proof_url' => 'payment-proofs/' . fake()->uuid() . '.jpg',
                        'notes' => fake()->optional()->sentence(),
                    ],
                ]);
            }
        }
    }

    /**
     * Generate item untuk service per_kg (dengan clothing types)
     */
    private function generatePerKgItem(Service $service, $clothingTypes): array
    {
        $pricePerKg = ServiceHelper::getPricePerKg($service);
        $totalWeight = fake()->randomFloat(2, 1, 20);

        // Generate clothing items (2-5 jenis pakaian)
        $clothingItems = [];
        $clothingCount = fake()->numberBetween(2, 5);

        for ($i = 0; $i < $clothingCount; $i++) {
            $clothingType = $clothingTypes->random();
            $clothingItems[] = [
                'clothing_type_id' => $clothingType->id,
                'clothing_type_name' => $clothingType->name,
                'quantity' => fake()->numberBetween(1, 10),
            ];
        }

        return [
            'service_id' => $service->id,
            'service_name' => $service->name,
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
    private function generatePerItemItem(Service $service): array
    {
        $pricePerItem = $service->data['pricing']['price_per_item'] ?? 50000;
        $quantity = fake()->numberBetween(1, 5);

        return [
            'service_id' => $service->id,
            'service_name' => $service->name,
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

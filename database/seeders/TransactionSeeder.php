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
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Seed data transactions dengan payments
     */
    public function run(): void
    {
        $customers = Customer::all();
        $services = Service::all();
        $couriers = Courier::all();
        $locations = Location::where('type', 'pos')->get();

        // Buat 100 transaksi
        for ($i = 0; $i < 100; $i++) {
            $customer = $customers->random();
            $service = $services->random();
            $courier = $couriers->random();
            $selectedLocation = $locations->random();

            $orderDate = fake()->dateTimeBetween('-3 months', 'now');
            $weight = fake()->randomFloat(2, 1, 20);
            $pricePerKg = ServiceHelper::getPricePerKg($service);
            $totalPrice = $weight * $pricePerKg;

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

            // Buat transaction dengan struktur JSONB
            $transaction = Transaction::create([
                'invoice_number' => 'INV/' . $orderDate->format('Ymd') . '/' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'courier_id' => $courier->id,
                'location_id' => $selectedLocation->id,
                'workflow_status' => $workflowStatus,
                'payment_status' => $isPaid ? 'paid' : 'unpaid',
                'data' => [
                    'items' => [
                        [
                            'service_id' => $service->id,
                            'service_name' => $service->name,
                            'weight' => $weight,
                            'price_per_kg' => $pricePerKg,
                            'subtotal' => $totalPrice,
                        ],
                    ],
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
                        'proof_url' => fake()->imageUrl(640, 480, 'payment', true),
                        'method' => fake()->randomElement(['cash', 'transfer', 'qris', 'e-wallet']),
                        'notes' => fake()->optional()->sentence(),
                    ],
                ]);
            }
        }
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

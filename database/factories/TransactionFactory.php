<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Helper\database\CustomerHelper;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Courier;
use App\Models\Location;
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
        $durationDays = fake()->randomElement([1, 2, 3, 5]);
        $estimatedFinishDate = (clone $orderDate)->modify("+{$durationDays} days");

        $weight = fake()->randomFloat(2, 1, 20);
        $pricePerKg = fake()->randomFloat(2, 5000, 15000);
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
        $isPaid = fake()->boolean(60);

        $formLoadedAt = (clone $orderDate)->modify('-' . fake()->numberBetween(5, 300) . ' seconds');

        // Generate customer untuk mendapatkan address
        $customer = Customer::factory()->make();
        $customerAddress = CustomerHelper::getDefaultAddress($customer);

        // Generate timeline berdasarkan workflow status
        $timeline = $this->generateTimeline($workflowStatus, $orderDate, $estimatedFinishDate);

        // Generate service items
        $serviceName = fake()->randomElement([
            'Cuci Kering',
            'Cuci Setrika',
            'Setrika Saja',
            'Cuci Express',
            'Cuci Premium',
            'Dry Clean',
        ]);

        return [
            'invoice_number' => 'INV/' . $orderDate->format('Ymd') . '/' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => Customer::factory(),
            'courier_id' => fake()->optional(0.8)->randomElement([null, Courier::factory()]),
            'location_id' => fake()->optional(0.7)->randomElement([null, Location::factory()->standalone()]),
            'workflow_status' => $workflowStatus,
            'payment_status' => $isPaid ? 'paid' : 'unpaid',
            'data' => [
                'items' => [
                    [
                        'service_id' => null, // Will be filled by seeder if needed
                        'service_name' => $serviceName,
                        'weight' => $weight,
                        'price_per_kg' => $pricePerKg,
                        'subtotal' => $totalPrice,
                    ],
                ],
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
                    'address' => 'Jl. Example No. 123, Mandonga, Mandonga, Kota Kendari',
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
     * Generate timeline based on workflow status
     */
    private function generateTimeline(string $workflowStatus, $orderDate, $estimatedFinishDate): array
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

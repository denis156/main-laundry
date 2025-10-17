<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Service;
use App\Models\CourierMotorcycle;
use App\Models\Pos;
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

        return [
            'invoice_number' => 'INV/' . $orderDate->format('Ymd') . '/' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => Customer::factory(),
            'service_id' => Service::factory(),
            'courier_motorcycle_id' => fake()->optional(0.8)->randomElement([null, CourierMotorcycle::factory()]),
            'pos_id' => fake()->optional(0.7)->randomElement([null, Pos::factory()]),
            'weight' => $weight,
            'price_per_kg' => $pricePerKg,
            'total_price' => $totalPrice,
            'workflow_status' => $workflowStatus,
            'payment_timing' => $paymentTiming,
            'payment_status' => $isPaid ? 'paid' : 'unpaid',
            'payment_proof_url' => $isPaid ? fake()->imageUrl(640, 480, 'payment', true) : null,
            'paid_at' => $isPaid ? fake()->dateTimeBetween($orderDate, 'now') : null,
            'notes' => fake()->optional()->sentence(),
            'order_date' => $orderDate,
            'estimated_finish_date' => $estimatedFinishDate,
            'actual_finish_date' => fake()->optional(0.7)->dateTimeBetween($orderDate, $estimatedFinishDate),
            'tracking_token' => fake()->uuid(),
            'customer_ip' => fake()->ipv4(),
            'customer_user_agent' => fake()->userAgent(),
            'form_loaded_at' => $formLoadedAt,
        ];
    }
}

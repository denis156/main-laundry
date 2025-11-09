<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Courier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentDate = fake()->dateTimeBetween('-3 months', 'now');
        $amount = fake()->randomFloat(2, 10000, 500000);
        $paymentMethod = fake()->randomElement(['cash', 'transfer', 'qris', 'e-wallet']);

        return [
            'transaction_id' => Transaction::factory(),
            'courier_id' => Courier::factory(),
            'amount' => $amount,
            'data' => [
                'payment_date' => $paymentDate->format('Y-m-d H:i:s'),
                'proof_url' => fake()->imageUrl(640, 480, 'payment', true),
                'method' => $paymentMethod,
                'notes' => fake()->optional()->sentence(),
            ],
        ];
    }

    /**
     * Indicate that the payment is cash
     */
    public function cash(): static
    {
        return $this->state(function (array $attributes) {
            $data = $attributes['data'] ?? [];
            $data['method'] = 'cash';
            $data['proof_url'] = null;
            return ['data' => $data];
        });
    }

    /**
     * Indicate that the payment is transfer
     */
    public function transfer(): static
    {
        return $this->state(function (array $attributes) {
            $data = $attributes['data'] ?? [];
            $data['method'] = 'transfer';
            return ['data' => $data];
        });
    }
}

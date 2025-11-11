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
        $paymentMethod = fake()->randomElement(['cash', 'transfer', 'qris']);

        return [
            'transaction_id' => Transaction::factory()->unpaid(), // Only generate for unpaid transactions
            'courier_id' => fn(array $attributes) => Transaction::find($attributes['transaction_id'])?->courier_id,
            'amount' => fn(array $attributes) => Transaction::find($attributes['transaction_id'])?->data['pricing']['total_price'] ?? fake()->randomFloat(2, 10000, 500000),
            'data' => [
                'payment_date' => $paymentDate->format('Y-m-d H:i:s'),
                'method' => $paymentMethod,
                'proof_url' => null, // Will be filled by actual file upload
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
            // proof_url tetap required bahkan untuk cash
            return ['data' => $data];
        });
    }

    /**
     * Indicate that the payment is qris
     */
    public function qris(): static
    {
        return $this->state(function (array $attributes) {
            $data = $attributes['data'] ?? [];
            $data['method'] = 'qris';
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

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionDetail>
 */
class TransactionDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $weight = fake()->randomFloat(2, 1, 10);
        $price = fake()->randomElement([5000, 7000, 8000, 10000, 12000, 15000]);
        $subtotal = $weight * $price;

        return [
            'transaction_id' => Transaction::factory(),
            'service_id' => Service::factory(),
            'weight' => $weight,
            'price' => $price,
            'subtotal' => $subtotal,
        ];
    }
}

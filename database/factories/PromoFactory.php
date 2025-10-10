<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promo>
 */
class PromoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['percentage', 'fixed']);
        $validFrom = fake()->dateTimeBetween('-1 month', '+1 week');
        $validUntil = fake()->dateTimeBetween($validFrom, '+2 months');

        return [
            'code' => strtoupper(fake()->unique()->lexify('????###')),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'type' => $type,
            'value' => $type === 'percentage' ? fake()->numberBetween(5, 50) : fake()->randomElement([5000, 10000, 15000, 20000, 25000]),
            'min_transaction' => fake()->randomElement([0, 50000, 100000, 150000]),
            'max_discount' => $type === 'percentage' ? fake()->optional()->randomElement([25000, 50000, 100000]) : null,
            'usage_limit' => fake()->optional()->numberBetween(10, 1000),
            'usage_count' => 0,
            'usage_per_user' => fake()->optional()->numberBetween(1, 5),
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
            'is_active' => fake()->boolean(80), // 80% aktif
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'customer_id' => Customer::factory(),
            'membership_tier_id' => MembershipTier::factory(),
            'member_number' => 'MBR-' . $date->format('Ymd') . '-' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'member_since' => $date,
            'total_points' => fake()->numberBetween(0, 10000),
            'is_active' => fake()->boolean(90), // 90% aktif
        ];
    }
}

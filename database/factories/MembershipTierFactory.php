<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MembershipTier>
 */
class MembershipTierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'min_points' => fake()->numberBetween(0, 1000) * 100,
            'discount_percentage' => fake()->randomElement([5, 10, 15, 20, 25]),
            'color' => fake()->hexColor(),
            'benefits' => [
                fake()->sentence(),
                fake()->sentence(),
                fake()->sentence(),
            ],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}

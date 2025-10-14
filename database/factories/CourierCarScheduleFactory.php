<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LoadingPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourierCarSchedule>
 */
class CourierCarScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loadingPostIds = LoadingPost::pluck('id')->toArray();
        $selectedPosts = fake()->randomElements($loadingPostIds, fake()->numberBetween(2, 5));

        return [
            'trip_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'departure_time' => fake()->time('H:i:s'),
            'trip_type' => fake()->randomElement(['pickup', 'delivery']),
            'loading_post_ids' => $selectedPosts,
            'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed', 'cancelled']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}

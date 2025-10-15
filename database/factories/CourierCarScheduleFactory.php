<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pos;
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
        $posIds = Pos::pluck('id')->toArray();
        $selectedPos = fake()->randomElements($posIds, fake()->numberBetween(2, 5));

        return [
            'trip_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'departure_time' => fake()->time('H:i:s'),
            'trip_type' => fake()->randomElement(['pickup', 'delivery']),
            'pos_ids' => $selectedPos,
            'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed', 'cancelled']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}

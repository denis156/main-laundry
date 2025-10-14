<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Resort;
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
        $resortIds = Resort::pluck('id')->toArray();
        $selectedResorts = fake()->randomElements($resortIds, fake()->numberBetween(2, 5));

        return [
            'trip_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'departure_time' => fake()->time('H:i:s'),
            'trip_type' => fake()->randomElement(['pickup', 'delivery']),
            'resort_ids' => $selectedResorts,
            'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed', 'cancelled']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}

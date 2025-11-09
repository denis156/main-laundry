<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Location;
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
        $tripDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $departureTime = fake()->time('H:i:s');
        $tripType = fake()->randomElement(['pickup', 'delivery']);
        $status = fake()->randomElement(['scheduled', 'in_progress', 'completed', 'cancelled']);

        // Get random POS locations
        $posLocations = Location::where('type', 'pos')->pluck('id')->toArray();
        $selectedLocationIds = !empty($posLocations)
            ? fake()->randomElements($posLocations, min(fake()->numberBetween(2, 5), count($posLocations)))
            : [];

        return [
            'trip_date' => $tripDate,
            'trip_type' => $tripType,
            'status' => $status,
            'data' => [
                'departure_time' => $departureTime,
                'location_ids' => $selectedLocationIds,
                'driver_info' => [
                    'name' => fake()->name(),
                    'phone' => fake()->numerify('8##########'),
                    'vehicle_number' => strtoupper(fake()->bothify('? #### ???')),
                ],
                'notes' => fake()->optional()->sentence(),
            ],
        ];
    }

    /**
     * Indicate that the schedule is for pickup trip
     */
    public function pickup(): static
    {
        return $this->state(fn (array $attributes) => [
            'trip_type' => 'pickup',
        ]);
    }

    /**
     * Indicate that the schedule is for delivery trip
     */
    public function delivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'trip_type' => 'delivery',
        ]);
    }

    /**
     * Indicate that the schedule is completed
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}

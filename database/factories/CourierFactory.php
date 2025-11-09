<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Courier>
 */
class CourierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password', // default password
            'assigned_location_id' => Location::factory(),
            'data' => [
                'name' => fake()->name(),
                'phone' => fake()->numerify('8##########'),
                'vehicle_number' => strtoupper(fake()->bothify('? #### ???')),
                'avatar_url' => fake()->optional()->imageUrl(200, 200, 'people'),
                'is_active' => fake()->boolean(85),
                'preferences' => [
                    'notification_enabled' => fake()->boolean(80),
                ],
            ],
        ];
    }

    /**
     * Indicate that the courier is assigned to a POS location
     */
    public function assignedToPos(): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_location_id' => Location::factory()->standalone(),
        ]);
    }

    /**
     * Indicate that the courier is active
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $data = $attributes['data'] ?? [];
            $data['is_active'] = true;
            return ['data' => $data];
        });
    }

    /**
     * Indicate that the courier is inactive
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            $data = $attributes['data'] ?? [];
            $data['is_active'] = false;
            return ['data' => $data];
        });
    }
}

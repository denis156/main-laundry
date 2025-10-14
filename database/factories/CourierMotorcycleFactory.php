<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Resort;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourierMotorcycle>
 */
class CourierMotorcycleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password', // default password
            'phone' => fake()->numerify('08##########'),
            'vehicle_number' => strtoupper(fake()->bothify('? #### ???')),
            'assigned_resort_id' => Resort::factory(),
            'avatar_url' => fake()->optional()->imageUrl(200, 200, 'people'),
            'is_active' => fake()->boolean(85),
        ];
    }
}

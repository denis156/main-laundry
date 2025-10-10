<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Member;
use App\Models\MembershipTier;
use Illuminate\Database\Seeder;

class CustomerMemberSeeder extends Seeder
{
    /**
     * Seed data customers dan members dengan berbagai tier
     */
    public function run(): void
    {
        $tiers = MembershipTier::all();

        // Buat 30 customers biasa (tanpa membership)
        Customer::factory()->count(30)->create();

        // Buat 50 customers dengan membership
        Customer::factory()
            ->count(50)
            ->create()
            ->each(function ($customer) use ($tiers) {
                // Random pilih tier
                $tier = $tiers->random();

                // Buat member dengan points yang sesuai tier
                Member::factory()->create([
                    'customer_id' => $customer->id,
                    'membership_tier_id' => $tier->id,
                    'total_points' => fake()->numberBetween($tier->min_points, $tier->min_points + 500),
                ]);
            });

        // Buat beberapa members dengan tier tertinggi
        Customer::factory()
            ->count(5)
            ->create()
            ->each(function ($customer) use ($tiers) {
                $diamondTier = $tiers->where('slug', 'diamond')->first();

                Member::factory()->create([
                    'customer_id' => $customer->id,
                    'membership_tier_id' => $diamondTier->id,
                    'total_points' => fake()->numberBetween(5000, 10000),
                ]);
            });
    }
}

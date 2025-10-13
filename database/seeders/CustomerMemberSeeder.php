<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerMemberSeeder extends Seeder
{
    /**
     * Seed data customers
     */
    public function run(): void
    {
        // Buat 100 customers
        Customer::factory()->count(100)->create();
    }
}

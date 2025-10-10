<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database aplikasi laundry
     */
    public function run(): void
    {
        $this->call([
            MasterDataSeeder::class,
            CustomerMemberSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}

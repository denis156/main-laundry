<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@laundry.com',
        ]);

        // Buat staff users spesifik
        User::factory()->create([
            'name' => 'Denis',
            'email' => 'denis@laundry.com',
        ]);

        User::factory()->create([
            'name' => 'Galih',
            'email' => 'galih@laundry.com',
        ]);

        User::factory()->create([
            'name' => 'Meli',
            'email' => 'meli@laundry.com',
        ]);

        User::factory()->create([
            'name' => 'Ahmad',
            'email' => 'ahmad@laundry.com',
        ]);

        User::factory()->create([
            'name' => 'Riski',
            'email' => 'riski@laundry.com',
        ]);

        User::factory()->create([
            'name' => 'Onda',
            'email' => 'onda@laundry.com',
        ]);
    }
}

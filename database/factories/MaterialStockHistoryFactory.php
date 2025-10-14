<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterialStockHistory>
 */
class MaterialStockHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['in', 'out']);
        $stockBefore = fake()->randomFloat(2, 50, 300);
        $quantity = fake()->randomFloat(2, 5, 100);

        $stockAfter = $type === 'in'
            ? $stockBefore + $quantity
            : $stockBefore - $quantity;

        $notesIn = [
            'Pembelian dari supplier',
            'Restocking bulanan',
            'Pembelian emergency',
            'Pengisian stock',
        ];

        $notesOut = [
            'Pemakaian operasional',
            'Untuk transaksi pelanggan',
            'Stock keluar untuk produksi',
            'Pemakaian harian',
        ];

        return [
            'material_id' => Material::factory(),
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => max(0, $stockAfter),
            'notes' => $type === 'in'
                ? fake()->randomElement($notesIn)
                : fake()->randomElement($notesOut),
            'created_by' => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}

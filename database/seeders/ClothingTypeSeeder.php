<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ClothingType;
use Illuminate\Database\Seeder;

class ClothingTypeSeeder extends Seeder
{
    /**
     * Seed data jenis pakaian
     */
    public function run(): void
    {
        $clothingTypes = [
            [
                'name' => 'Kemeja',
                'description' => 'Kemeja lengan panjang atau pendek, formal maupun kasual',
                'care_instructions' => ['Setrika suhu sedang', 'Cuci dengan air dingin', 'Hindari pemutih'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Celana Panjang',
                'description' => 'Celana panjang formal, jeans, chino, atau kasual',
                'care_instructions' => ['Hindari pemutih', 'Setrika suhu sedang', 'Cuci warna terpisah'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Celana Pendek',
                'description' => 'Celana pendek kasual, olahraga, atau formal',
                'care_instructions' => ['Cuci dengan air dingin', 'Jemur di tempat teduh'],
                'sort_order' => 3,
            ],
            [
                'name' => 'Kaos/T-Shirt',
                'description' => 'Kaos oblong lengan pendek atau panjang',
                'care_instructions' => ['Cuci dengan air dingin', 'Hindari setrika langsung pada gambar', 'Jemur terbalik'],
                'sort_order' => 4,
            ],
            [
                'name' => 'Rok',
                'description' => 'Rok panjang, pendek, atau midi',
                'care_instructions' => ['Setrika suhu rendah-sedang', 'Cuci dengan pewangi lembut', 'Hindari mesin pengering'],
                'sort_order' => 5,
            ],
            [
                'name' => 'Dress',
                'description' => 'Gaun atau dress formal maupun kasual',
                'care_instructions' => ['Cuci dengan hati-hati', 'Setrika suhu rendah', 'Dry clean untuk bahan sensitif'],
                'sort_order' => 6,
            ],
            [
                'name' => 'Jaket',
                'description' => 'Jaket tipis, tebal, atau blazer',
                'care_instructions' => ['Dry clean direkomendasikan', 'Hindari mesin pengering', 'Gantung saat penyimpanan'],
                'sort_order' => 7,
            ],
            [
                'name' => 'Sweater/Hoodie',
                'description' => 'Sweater, hoodie, atau cardigan',
                'care_instructions' => ['Cuci dengan air dingin', 'Jangan diperas', 'Jemur mendatar', 'Hindari mesin pengering'],
                'sort_order' => 8,
            ],
            [
                'name' => 'Jilbab/Kerudung',
                'description' => 'Hijab, pashmina, atau kerudung',
                'care_instructions' => ['Cuci dengan detergen lembut', 'Hindari pemutih', 'Setrika suhu rendah'],
                'sort_order' => 9,
            ],
            [
                'name' => 'Mukena',
                'description' => 'Mukena untuk sholat',
                'care_instructions' => ['Cuci terpisah', 'Setrika suhu rendah', 'Hindari pemutih'],
                'sort_order' => 10,
            ],
            [
                'name' => 'Handuk',
                'description' => 'Handuk mandi atau handuk kecil',
                'care_instructions' => ['Cuci dengan air panas', 'Boleh diputihkan', 'Jemur di bawah sinar matahari'],
                'sort_order' => 11,
            ],
            [
                'name' => 'Sprei',
                'description' => 'Seprai atau bed cover',
                'care_instructions' => ['Cuci dengan air hangat', 'Setrika suhu sedang', 'Cuci warna terpisah'],
                'sort_order' => 12,
            ],
            [
                'name' => 'Selimut',
                'description' => 'Selimut tipis atau tebal',
                'care_instructions' => ['Cuci dengan mesin kapasitas besar', 'Jemur hingga kering sempurna', 'Boleh dry clean'],
                'sort_order' => 13,
            ],
            [
                'name' => 'Karpet Kecil',
                'description' => 'Karpet atau permadani ukuran kecil-sedang',
                'care_instructions' => ['Cuci khusus karpet', 'Jemur di tempat yang luas', 'Keringkan sempurna'],
                'sort_order' => 14,
            ],
            [
                'name' => 'Gordyn',
                'description' => 'Gorden atau tirai',
                'care_instructions' => ['Cuci dengan hati-hati', 'Hindari pemutih', 'Setrika saat masih sedikit lembab'],
                'sort_order' => 15,
            ],
        ];

        foreach ($clothingTypes as $clothingType) {
            ClothingType::create([
                'name' => $clothingType['name'],
                'data' => [
                    'description' => $clothingType['description'],
                    'care_instructions' => $clothingType['care_instructions'],
                ],
                'is_active' => true,
                'sort_order' => $clothingType['sort_order'],
            ]);
        }
    }
}

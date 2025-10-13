<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Promo;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Seed data transactions dengan details dan payments
     */
    public function run(): void
    {
        $customers = Customer::all();
        $services = Service::all();
        $users = User::all();
        $promos = Promo::where('is_active', true)->get();

        // Buat 100 transaksi
        for ($i = 0; $i < 100; $i++) {
            $customer = $customers->random();
            $user = $users->random();

            // 40% kemungkinan ada promo
            $promo = fake()->optional(0.4)->randomElement($promos->toArray());

            // Buat transaction
            $transaction = Transaction::factory()->create([
                'customer_id' => $customer->id,
                'promo_id' => $promo?->id ?? null,
                'user_id' => $user->id,
            ]);

            // Buat 1-5 transaction details untuk setiap transaction
            $detailCount = fake()->numberBetween(1, 5);
            for ($j = 0; $j < $detailCount; $j++) {
                TransactionDetail::factory()->create([
                    'transaction_id' => $transaction->id,
                    'service_id' => $services->random()->id,
                ]);
            }

            // Buat payment jika sudah dibayar
            if ($transaction->payment_status !== 'unpaid') {
                // Bisa ada 1-3 pembayaran (cicilan)
                $paymentCount = fake()->numberBetween(1, 3);
                $remainingAmount = $transaction->paid_amount;

                for ($k = 0; $k < $paymentCount; $k++) {
                    if ($remainingAmount <= 0) {
                        break;
                    }

                    $paymentAmount = $k === $paymentCount - 1
                        ? $remainingAmount // Pembayaran terakhir: sisa amount
                        : fake()->randomFloat(2, 10000, min($remainingAmount, $transaction->total_price / 2));

                    Payment::factory()->create([
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'amount' => $paymentAmount,
                        'payment_date' => fake()->dateTimeBetween($transaction->order_date, 'now'),
                    ]);

                    $remainingAmount -= $paymentAmount;
                }
            }
        }
    }
}

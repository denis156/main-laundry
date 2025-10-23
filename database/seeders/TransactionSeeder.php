<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\CourierMotorcycle;
use App\Models\Pos;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Seed data transactions dengan payments
     */
    public function run(): void
    {
        $customers = Customer::all();
        $services = Service::all();
        $couriers = CourierMotorcycle::all();
        $pos = Pos::all();

        // Buat 100 transaksi
        for ($i = 0; $i < 100; $i++) {
            $customer = $customers->random();
            $service = $services->random();
            $courier = $couriers->random();
            $selectedPos = $pos->random();

            $orderDate = fake()->dateTimeBetween('-3 months', 'now');
            $weight = fake()->randomFloat(2, 1, 20);
            $pricePerKg = $service->price_per_kg;
            $totalPrice = $weight * $pricePerKg;

            $workflowStatus = fake()->randomElement([
                'pending_confirmation',
                'confirmed',
                'picked_up',
                'at_loading_post',
                'in_washing',
                'washing_completed',
                'out_for_delivery',
                'delivered',
                'cancelled',
            ]);

            $paymentTiming = fake()->randomElement(['on_pickup', 'on_delivery']);
            $isPaid = fake()->boolean(70);

            // Buat transaction
            $transaction = Transaction::factory()->create([
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'courier_motorcycle_id' => $courier->id,
                'pos_id' => $selectedPos->id,
                'weight' => $weight,
                'price_per_kg' => $pricePerKg,
                'total_price' => $totalPrice,
                'workflow_status' => $workflowStatus,
                'payment_timing' => $paymentTiming,
                'payment_status' => $isPaid ? 'paid' : 'unpaid',
                'order_date' => $orderDate,
            ]);

            // Buat payment jika sudah dibayar
            if ($isPaid) {
                Payment::factory()->create([
                    'transaction_id' => $transaction->id,
                    'courier_motorcycle_id' => $courier->id,
                    'amount' => $totalPrice,
                    'payment_date' => fake()->dateTimeBetween($orderDate, 'now'),
                ]);
            }
        }
    }
}

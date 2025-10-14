<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\CourierMotorcycle;
use App\Models\LoadingPost;
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
        $loadingPosts = LoadingPost::all();

        // Buat 100 transaksi
        for ($i = 0; $i < 100; $i++) {
            $customer = $customers->random();
            $service = $services->random();
            $courier = $couriers->random();
            $loadingPost = $loadingPosts->random();

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
                'loading_post_id' => $loadingPost->id,
                'weight' => $weight,
                'price_per_kg' => $pricePerKg,
                'total_price' => $totalPrice,
                'workflow_status' => $workflowStatus,
                'payment_timing' => $paymentTiming,
                'payment_status' => $isPaid ? 'paid' : 'unpaid',
                'paid_at' => $isPaid ? fake()->dateTimeBetween($orderDate, 'now') : null,
                'order_date' => $orderDate,
            ]);

            // Buat payment jika sudah dibayar
            if ($isPaid) {
                Payment::factory()->create([
                    'transaction_id' => $transaction->id,
                    'courier_motorcycle_id' => $courier->id,
                    'amount' => $totalPrice,
                    'payment_date' => $transaction->paid_at,
                ]);
            }
        }
    }
}

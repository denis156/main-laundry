<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Member;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderDate = fake()->dateTimeBetween('-3 months', 'now');
        $durationDays = fake()->randomElement([1, 2, 3, 5]);
        $estimatedFinishDate = (clone $orderDate)->modify("+{$durationDays} days");

        $subtotal = fake()->randomFloat(2, 20000, 500000);
        $memberDiscountPercentage = fake()->optional(0.5)->randomElement([0, 5, 10, 15, 20]);
        $memberDiscountAmount = $memberDiscountPercentage ? ($subtotal * $memberDiscountPercentage) / 100 : 0;
        $promoDiscountAmount = fake()->optional(0.3)->randomFloat(2, 5000, 50000) ?? 0;
        $totalDiscountAmount = $memberDiscountAmount + $promoDiscountAmount;
        $totalPrice = $subtotal - $totalDiscountAmount;
        $paidAmount = fake()->randomElement([0, $totalPrice * 0.5, $totalPrice]);

        return [
            'invoice_number' => 'INV/' . $orderDate->format('Ymd') . '/' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => Customer::factory(),
            'member_id' => fake()->optional(0.6)->randomElement([null, Member::factory()]),
            'promo_id' => fake()->optional(0.3)->randomElement([null, Promo::factory()]),
            'user_id' => User::factory(),
            'total_weight' => fake()->randomFloat(2, 1, 20),
            'subtotal' => $subtotal,
            'member_discount_amount' => $memberDiscountAmount,
            'member_discount_percentage' => $memberDiscountPercentage ?? 0,
            'promo_discount_amount' => $promoDiscountAmount,
            'total_discount_amount' => $totalDiscountAmount,
            'total_price' => $totalPrice,
            'points_earned' => (int) floor($totalPrice / 10000),
            'status' => fake()->randomElement(['pending', 'process', 'ready', 'completed', 'cancelled']),
            'payment_status' => $paidAmount >= $totalPrice ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid'),
            'paid_amount' => $paidAmount,
            'notes' => fake()->optional()->sentence(),
            'order_date' => $orderDate,
            'estimated_finish_date' => $estimatedFinishDate,
            'actual_finish_date' => fake()->optional(0.7)->dateTimeBetween($orderDate, $estimatedFinishDate),
        ];
    }
}

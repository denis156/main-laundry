<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\Member;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private InvoiceService $invoiceService,
        private PointService $pointService,
        private MemberService $memberService
    ) {}

    /**
     * Buat transaksi baru dengan detail
     *
     * @param array $data Data transaksi
     * @param array $items Array items [['service_id' => 1, 'weight' => 2.5], ...]
     */
    public function createTransaction(
        Customer $customer,
        User $user,
        array $items,
        ?Member $member = null,
        ?string $notes = null
    ): Transaction {
        return DB::transaction(function () use ($customer, $user, $items, $member, $notes) {
            // Hitung subtotal dari items
            $subtotal = 0;
            $totalWeight = 0;
            $itemsData = [];

            foreach ($items as $item) {
                $service = Service::findOrFail($item['service_id']);
                $weight = $item['weight'];
                $itemSubtotal = $weight * $service->price_per_kg;

                $totalWeight += $weight;
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'service' => $service,
                    'weight' => $weight,
                    'price' => $service->price_per_kg,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Hitung diskon jika member
            $discountPercentage = 0;
            $discountAmount = 0;

            if ($member && $this->memberService->isActive($member)) {
                $discountPercentage = $this->memberService->getDiscountPercentage($member);
                $discountAmount = ($subtotal * $discountPercentage) / 100;
            }

            // Hitung harga final
            $totalPrice = $subtotal - $discountAmount;

            // Hitung poin yang didapat (hanya dari harga final)
            $pointsEarned = 0;
            if ($member && $this->memberService->isActive($member)) {
                $pointsEarned = $this->pointService->calculatePointsFromAmount($totalPrice);
            }

            // Ambil service dengan durasi terlama untuk estimasi selesai
            $maxDuration = collect($itemsData)->max(fn($item) => $item['service']->duration_days);
            $orderDate = Carbon::now();
            $estimatedFinishDate = $orderDate->copy()->addDays($maxDuration);

            // Buat transaksi
            $transaction = Transaction::create([
                'invoice_number' => $this->invoiceService->generateInvoiceNumber(),
                'customer_id' => $customer->id,
                'member_id' => $member?->id,
                'user_id' => $user->id,
                'total_weight' => $totalWeight,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_percentage' => $discountPercentage,
                'total_price' => $totalPrice,
                'points_earned' => $pointsEarned,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'paid_amount' => 0,
                'notes' => $notes,
                'order_date' => $orderDate,
                'estimated_finish_date' => $estimatedFinishDate,
            ]);

            // Buat detail transaksi
            foreach ($itemsData as $itemData) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'service_id' => $itemData['service']->id,
                    'weight' => $itemData['weight'],
                    'price' => $itemData['price'],
                    'subtotal' => $itemData['subtotal'],
                ]);
            }

            return $transaction->fresh(['customer', 'member', 'user', 'transactionDetails.service']);
        });
    }

    /**
     * Update status transaksi
     */
    public function updateStatus(Transaction $transaction, string $status): void
    {
        $validStatuses = ['pending', 'process', 'ready', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Status tidak valid: {$status}");
        }

        $transaction->update(['status' => $status]);

        // Jika completed, set tanggal selesai aktual dan berikan poin
        if ($status === 'completed' && !$transaction->actual_finish_date) {
            $transaction->update(['actual_finish_date' => Carbon::now()]);

            // Berikan poin jika member dan pembayaran sudah lunas
            if ($transaction->member && $transaction->payment_status === 'paid' && $transaction->points_earned > 0) {
                $this->memberService->addPoints($transaction->member, $transaction->points_earned);
            }
        }
    }

    /**
     * Update status pembayaran
     */
    public function updatePaymentStatus(Transaction $transaction): void
    {
        if ($transaction->paid_amount >= $transaction->total_price) {
            $transaction->update(['payment_status' => 'paid']);

            // Berikan poin jika transaksi completed dan memiliki member
            if ($transaction->status === 'completed' && $transaction->member && $transaction->points_earned > 0) {
                $this->memberService->addPoints($transaction->member, $transaction->points_earned);
            }
        } elseif ($transaction->paid_amount > 0) {
            $transaction->update(['payment_status' => 'partial']);
        } else {
            $transaction->update(['payment_status' => 'unpaid']);
        }
    }

    /**
     * Hitung sisa pembayaran
     */
    public function getRemainingPayment(Transaction $transaction): float
    {
        return max(0, $transaction->total_price - $transaction->paid_amount);
    }

    /**
     * Cek apakah transaksi sudah lunas
     */
    public function isFullyPaid(Transaction $transaction): bool
    {
        return $transaction->paid_amount >= $transaction->total_price;
    }

    /**
     * Batalkan transaksi
     */
    public function cancelTransaction(Transaction $transaction): void
    {
        if ($transaction->status === 'completed') {
            throw new \Exception('Tidak dapat membatalkan transaksi yang sudah selesai');
        }

        $transaction->update(['status' => 'cancelled']);
    }
}

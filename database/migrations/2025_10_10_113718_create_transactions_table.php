<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('promo_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_weight', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('member_discount_amount', 10, 2)->default(0);
            $table->decimal('member_discount_percentage', 5, 2)->default(0);
            $table->decimal('promo_discount_amount', 10, 2)->default(0);
            $table->decimal('total_discount_amount', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->integer('points_earned')->default(0);
            $table->enum('status', ['pending', 'process', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->datetime('order_date');
            $table->datetime('estimated_finish_date');
            $table->datetime('actual_finish_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

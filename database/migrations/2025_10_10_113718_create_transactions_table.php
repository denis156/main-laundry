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
            $table->id()->comment('ID unik transaksi');
            $table->string('invoice_number')->unique()->comment('Nomor invoice (unik, format: INV/YYYYMMDD/XXXX)');
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete()->comment('ID customer (cascade on delete)');
            $table->foreignId('promo_id')->nullable()->constrained()->nullOnDelete()->comment('ID promo yang digunakan (null on delete)');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('ID kasir yang input (cascade on delete)');
            $table->decimal('total_weight', 10, 2)->comment('Total berat cucian (kg)');
            $table->decimal('subtotal', 10, 2)->comment('Subtotal sebelum diskon (Rp)');
            $table->decimal('promo_discount_amount', 10, 2)->default(0)->comment('Nominal diskon promo (Rp)');
            $table->decimal('total_discount_amount', 10, 2)->default(0)->comment('Total semua diskon (Rp)');
            $table->decimal('total_price', 10, 2)->comment('Total harga akhir setelah diskon (Rp)');
            $table->enum('status', ['pending', 'process', 'ready', 'completed', 'cancelled'])->default('pending')->comment('Status proses cucian');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->comment('Status pembayaran');
            $table->decimal('paid_amount', 10, 2)->default(0)->comment('Jumlah yang sudah dibayar (Rp)');
            $table->text('notes')->nullable()->comment('Catatan transaksi');
            $table->datetime('order_date')->comment('Tanggal order');
            $table->datetime('estimated_finish_date')->comment('Estimasi selesai');
            $table->datetime('actual_finish_date')->nullable()->comment('Tanggal selesai aktual');
            $table->timestamps();
            $table->softDeletes();
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

<?php

declare(strict_types=1);

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
            $table->foreignId('service_id')->constrained()->cascadeOnDelete()->comment('ID layanan yang dipilih (cascade on delete)');
            $table->foreignId('courier_motorcycle_id')->nullable()->constrained('couriers_motorcycle')->nullOnDelete()->comment('ID kurir motor yang handle (null on delete)');
            $table->foreignId('pos_id')->nullable()->constrained('pos')->nullOnDelete()->comment('ID pos transit (null on delete)');
            $table->decimal('weight', 10, 2)->default(0)->comment('Berat cucian ditimbang kurir (kg)');
            $table->decimal('price_per_kg', 10, 2)->default(0)->comment('Harga per kg saat transaksi untuk historical record (Rp)');
            $table->decimal('total_price', 10, 2)->default(0)->comment('Total harga final (weight × price_per_kg) (Rp)');
            $table->enum('workflow_status', ['pending_confirmation', 'confirmed', 'picked_up', 'at_loading_post', 'in_washing', 'washing_completed', 'out_for_delivery', 'delivered', 'cancelled'])->default('pending_confirmation')->comment('Status workflow transaksi');
            $table->enum('payment_timing', ['on_pickup', 'on_delivery'])->comment('Kapan customer bayar');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->comment('Status pembayaran');
            $table->string('payment_proof_url')->nullable()->comment('URL bukti screenshot pembayaran');
            $table->datetime('paid_at')->nullable()->comment('Waktu pembayaran dilakukan');
            $table->text('notes')->nullable()->comment('Catatan transaksi');
            $table->datetime('order_date')->comment('Tanggal order');
            $table->datetime('estimated_finish_date')->nullable()->comment('Estimasi selesai');
            $table->datetime('actual_finish_date')->nullable()->comment('Tanggal selesai aktual');
            $table->string('tracking_token', 36)->unique()->index()->comment('Token unik untuk tracking pesanan');
            $table->string('customer_ip', 45)->nullable()->comment('IP address customer saat order (support IPv6)');
            $table->text('customer_user_agent')->nullable()->comment('Browser user agent customer saat order');
            $table->datetime('form_loaded_at')->nullable()->comment('Waktu form dimuat untuk detect bot submission');
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

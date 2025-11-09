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
            $table->id()->comment('ID transaksi');
            $table->string('invoice_number')->unique()->comment('Nomor invoice unik');
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete()->comment('ID customer');
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete()->comment('ID kurir yang handle');
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete()->comment('ID lokasi (POS/Resort)');
            $table->string('workflow_status')->default('pending_confirmation')->comment('Status workflow transaksi (flexible)');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->comment('Status pembayaran (keamanan data)');
            $table->jsonb('data')->nullable()->comment('Data transaksi: items, pricing, customer_address, notes, metadata, tracking, timeline, anti_bot');
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performance
            $table->index('invoice_number');
            $table->index('workflow_status');
            $table->index('payment_status');
            $table->index(['customer_id', 'workflow_status']);
            $table->index(['courier_id', 'workflow_status']);
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

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
        Schema::create('payments', function (Blueprint $table) {
            $table->id()->comment('ID pembayaran');
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete()->comment('ID transaksi');
            $table->foreignId('courier_id')->constrained('couriers')->cascadeOnDelete()->comment('ID kurir yang upload bukti');
            $table->decimal('amount', 15, 2)->comment('Jumlah pembayaran');
            $table->jsonb('data')->nullable()->comment('Data pembayaran: payment_date, proof_url, method, notes');
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performance
            $table->index('transaction_id');
            $table->index('courier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

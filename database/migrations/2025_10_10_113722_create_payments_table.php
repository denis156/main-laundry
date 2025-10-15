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
            $table->id()->comment('ID unik pembayaran');
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete()->comment('ID transaksi (cascade on delete)');
            $table->foreignId('courier_motorcycle_id')->constrained('couriers_motorcycle')->cascadeOnDelete()->comment('ID kurir motor yang upload bukti (cascade on delete)');
            $table->decimal('amount', 10, 2)->comment('Jumlah pembayaran (Rp)');
            $table->string('payment_proof_url')->comment('URL screenshot bukti pembayaran');
            $table->datetime('payment_date')->comment('Tanggal dan waktu pembayaran');
            $table->text('notes')->nullable()->comment('Catatan pembayaran');
            $table->timestamps();
            $table->softDeletes();
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

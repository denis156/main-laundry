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
        Schema::create('payments', function (Blueprint $table) {
            $table->id()->comment('ID unik pembayaran');
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete()->comment('ID transaksi (cascade on delete)');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('ID kasir yang terima pembayaran (cascade on delete)');
            $table->decimal('amount', 10, 2)->comment('Jumlah pembayaran (Rp)');
            $table->enum('payment_method', ['cash', 'transfer', 'qris', 'debit', 'credit'])->comment('Metode pembayaran');
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

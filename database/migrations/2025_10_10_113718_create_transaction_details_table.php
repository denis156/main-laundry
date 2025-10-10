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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id()->comment('ID unik detail transaksi');
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete()->comment('ID transaksi (cascade on delete)');
            $table->foreignId('service_id')->constrained()->cascadeOnDelete()->comment('ID layanan yang dipilih (cascade on delete)');
            $table->decimal('weight', 10, 2)->comment('Berat cucian untuk service ini (kg)');
            $table->decimal('price', 10, 2)->comment('Harga per kg saat transaksi (Rp)');
            $table->decimal('subtotal', 10, 2)->comment('Subtotal item (weight × price)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};

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
        Schema::create('promos', function (Blueprint $table) {
            $table->id()->comment('ID unik promo');
            $table->string('code')->unique()->comment('Kode promo (unik)');
            $table->string('name')->comment('Nama promo');
            $table->text('description')->nullable()->comment('Deskripsi promo');
            $table->enum('type', ['percentage', 'fixed'])->comment('Tipe promo (percentage/fixed)');
            $table->decimal('value', 10, 2)->comment('Nilai diskon (% atau Rp)');
            $table->decimal('min_transaction', 10, 2)->default(0)->comment('Minimum transaksi untuk pakai promo (Rp)');
            $table->decimal('max_discount', 10, 2)->nullable()->comment('Maksimal diskon untuk tipe percentage (Rp)');
            $table->integer('usage_limit')->nullable()->comment('Batas total penggunaan promo');
            $table->integer('usage_count')->default(0)->comment('Jumlah promo yang sudah digunakan');
            $table->integer('usage_per_user')->nullable()->comment('Batas penggunaan per user/customer');
            $table->datetime('valid_from')->comment('Tanggal mulai berlaku promo');
            $table->datetime('valid_until')->comment('Tanggal berakhir promo');
            $table->boolean('is_active')->default(true)->comment('Status aktif promo (true/false)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};

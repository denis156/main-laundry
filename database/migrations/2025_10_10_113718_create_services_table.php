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
        Schema::create('services', function (Blueprint $table) {
            $table->id()->comment('ID unik service');
            $table->string('name')->comment('Nama layanan (Cuci Kering, Cuci Setrika, dll)');
            $table->decimal('price_per_kg', 10, 2)->comment('Harga per kilogram (Rp)');
            $table->integer('duration_days')->comment('Durasi pengerjaan (hari)');
            $table->boolean('is_active')->default(true)->comment('Status aktif service (true/false)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};

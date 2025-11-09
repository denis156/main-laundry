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
        Schema::create('clothing_types', function (Blueprint $table) {
            $table->id()->comment('ID jenis pakaian');
            $table->string('name')->comment('Nama jenis pakaian (contoh: Kemeja, Celana Panjang, Selimut)');
            $table->jsonb('data')->nullable()->comment('Data tambahan: description, icon, color, typical_weight_kg, care_instructions');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->integer('sort_order')->default(0)->comment('Urutan tampilan');
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performance
            $table->index('is_active');
            $table->index('sort_order');
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clothing_types');
    }
};

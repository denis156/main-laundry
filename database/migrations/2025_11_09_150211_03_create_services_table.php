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
        Schema::create('services', function (Blueprint $table) {
            $table->id()->comment('ID layanan');
            $table->string('name')->comment('Nama layanan');
            $table->boolean('is_featured')->default(false)->comment('Layanan unggulan/recommended (tampil di banner/badge)');
            $table->integer('sort_order')->default(0)->comment('Urutan tampilan (semakin kecil semakin depan)');
            $table->jsonb('data')->nullable()->comment('Data layanan: service_type, pricing, pricing_tiers, duration_days, features, includes, restrictions, materials_used, icon, color, badge_settings');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performance
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('sort_order');
            $table->index(['is_active', 'is_featured', 'sort_order']);
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

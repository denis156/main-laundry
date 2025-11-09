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
        Schema::create('resources', function (Blueprint $table) {
            $table->id()->comment('ID resource');
            $table->enum('type', ['equipment', 'material'])->comment('Jenis resource: equipment (aset) atau material (habis pakai)');
            $table->string('name')->comment('Nama resource');
            $table->jsonb('data')->nullable()->comment('Data resource: stocks, pricing, supplier, maintenance, usage_rate, certifications, storage, dll');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performance
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};

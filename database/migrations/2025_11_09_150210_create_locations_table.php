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
        Schema::create('locations', function (Blueprint $table) {
            $table->id()->comment('ID lokasi');
            $table->enum('type', ['resort', 'pos'])->comment('Jenis lokasi: resort (cabang utama) atau pos (transit)');
            $table->foreignId('parent_id')->nullable()->constrained('locations')->nullOnDelete()->comment('Parent location (untuk POS yang punya resort induk)');
            $table->string('name')->comment('Nama lokasi');
            $table->jsonb('data')->nullable()->comment('Data lokasi: location, coverage_area, operating_hours, contact, facilities, capacity, metadata');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performance
            $table->index('type');
            $table->index('parent_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};

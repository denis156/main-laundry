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
        Schema::create('courier_cars_schedules', function (Blueprint $table) {
            $table->id()->comment('ID jadwal kurir mobil');
            $table->date('trip_date')->comment('Tanggal trip');
            $table->enum('trip_type', ['pickup', 'delivery'])->comment('Jenis trip: pickup (dari POS ke resort) atau delivery (dari resort ke POS)');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled')->comment('Status trip');
            $table->jsonb('data')->nullable()->comment('Data schedule: departure_time, location_ids, route, driver_info, notes');
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performance
            $table->index('trip_date');
            $table->index('trip_type');
            $table->index('status');
            $table->index(['trip_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_cars_schedules');
    }
};

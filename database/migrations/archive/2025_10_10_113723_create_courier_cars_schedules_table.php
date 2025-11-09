<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_cars_schedules', function (Blueprint $table) {
            $table->id()->comment('ID unik jadwal kurir mobil');
            $table->date('trip_date')->comment('Tanggal perjalanan');
            $table->time('departure_time')->comment('Waktu keberangkatan');
            $table->enum('trip_type', ['pickup', 'delivery'])->comment('Jenis trip: pickup (ambil dari pos) atau delivery (antar ke pos)');
            $table->json('pos_ids')->comment('Array ID pos yang dikunjungi (JSON)');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled')->comment('Status perjalanan');
            $table->text('notes')->nullable()->comment('Catatan trip');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_cars_schedules');
    }
};

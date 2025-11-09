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
        Schema::create('couriers', function (Blueprint $table) {
            $table->id()->comment('ID kurir');
            $table->string('email')->unique()->comment('Email untuk login');
            $table->string('password')->comment('Password terenkripsi');
            $table->foreignId('assigned_location_id')->constrained('locations')->cascadeOnDelete()->comment('Lokasi (POS) tetap yang ditugaskan');
            $table->jsonb('data')->nullable()->comment('Data kurir: name, phone, vehicle_number, avatar_url, is_active, preferences');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};

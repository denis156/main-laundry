<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('couriers_motorcycle', function (Blueprint $table) {
            $table->id()->comment('ID unik kurir motor');
            $table->string('name')->comment('Nama kurir motor');
            $table->string('email')->unique()->comment('Email untuk login (unik)');
            $table->string('password')->comment('Password terenkripsi');
            $table->string('phone')->comment('Nomor telepon/WA kurir');
            $table->string('vehicle_number')->comment('Nomor kendaraan (B1234XYZ)');
            $table->foreignId('assigned_resort_id')->constrained('resorts')->cascadeOnDelete()->comment('Resort tetap yang ditugaskan (cascade on delete)');
            $table->string('avatar_url')->nullable()->comment('URL foto profil');
            $table->boolean('is_active')->default(true)->comment('Status aktif kurir (true/false)');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couriers_motorcycle');
    }
};

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
        Schema::create('customers', function (Blueprint $table) {
            $table->id()->comment('ID unik customer');
            $table->string('name')->comment('Nama lengkap customer');
            $table->string('phone')->unique()->nullable()->comment('Nomor telepon/WhatsApp customer (unik)');
            $table->string('email')->nullable()->comment('Email customer');
            $table->string('password')->comment('Password terenkripsi');
            $table->string('avatar_url')->nullable()->comment('URL foto profil');

            // OAuth 2.0 Google
            $table->string('google_id')->nullable()->unique()->comment('Google OAuth user ID');
            $table->text('google_token')->nullable()->comment('Google OAuth access token');
            $table->text('google_refresh_token')->nullable()->comment('Google OAuth refresh token');

            // Wilayah (Kota Kendari, Sulawesi Tenggara)
            $table->string('district_code')->nullable()->comment('Kode kecamatan (dari API wilayah.id)');
            $table->string('district_name')->nullable()->comment('Nama kecamatan');
            $table->string('village_code')->nullable()->comment('Kode kelurahan (dari API wilayah.id)');
            $table->string('village_name')->nullable()->comment('Nama kelurahan');
            $table->text('detail_address')->nullable()->comment('Detail alamat (Jl, RT/RW, nomor rumah, patokan)');
            $table->text('address')->nullable()->comment('Alamat lengkap gabungan (untuk display & backward compatibility)');

            $table->boolean('member')->default(false)->comment('Status member customer (true/false)');
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
        Schema::dropIfExists('customers');
    }
};

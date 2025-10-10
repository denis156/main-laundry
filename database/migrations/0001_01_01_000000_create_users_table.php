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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('ID unik pengguna');
            $table->string('name')->comment('Nama lengkap pengguna');
            $table->string('email')->unique()->comment('Email pengguna (unik)');
            $table->timestamp('email_verified_at')->nullable()->comment('Waktu verifikasi email');
            $table->string('password')->comment('Password terenkripsi');
            $table->boolean('super_admin')->default(false)->comment('Status super admin (true/false)');
            $table->string('phone')->nullable()->comment('Nomor telepon pengguna');
            $table->string('avatar_url')->nullable()->comment('URL foto profil pengguna');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

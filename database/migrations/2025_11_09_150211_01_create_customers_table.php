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
            $table->id()->comment('ID customer');
            $table->string('email')->nullable()->unique()->comment('Email (untuk login atau Google OAuth)');
            $table->string('phone')->nullable()->unique()->comment('Nomor telepon (untuk login WA)');
            $table->string('password')->comment('Password terenkripsi');
            $table->jsonb('data')->nullable()->comment('Data customer: name, addresses, preferences, google_oauth, member, avatar_url');
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

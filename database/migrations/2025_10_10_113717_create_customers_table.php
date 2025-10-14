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
            $table->string('phone')->unique()->comment('Nomor telepon/WhatsApp customer (unik)');
            $table->string('email')->nullable()->comment('Email customer');
            $table->text('address')->nullable()->comment('Alamat lengkap customer');
            $table->boolean('member')->default(false)->comment('Status member customer (true/false)');
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

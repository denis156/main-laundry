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
        Schema::create('pos', function (Blueprint $table) {
            $table->id()->comment('ID unik pos');
            $table->foreignId('resort_id')->nullable()->constrained('resorts')->nullOnDelete()->comment('ID resort induk (null jika pos berdiri sendiri)');
            $table->string('name')->comment('Nama pos');
            $table->text('address')->comment('Alamat lengkap pos');
            $table->string('phone')->comment('Nomor telepon pos');
            $table->string('pic_name')->comment('Nama penanggung jawab pos');
            $table->string('area')->nullable()->comment('Area yang dilayani pos ini');
            $table->boolean('is_active')->default(true)->comment('Status aktif pos (true/false)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos');
    }
};

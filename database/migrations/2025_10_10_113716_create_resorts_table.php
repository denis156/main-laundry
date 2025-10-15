<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resorts', function (Blueprint $table) {
            $table->id()->comment('ID unik resort');
            $table->string('name')->comment('Nama resort (Resort Jakarta Selatan)');
            $table->text('address')->comment('Alamat lengkap resort');
            $table->string('phone')->comment('Nomor telepon resort');
            $table->string('pic_name')->comment('Nama penanggung jawab resort');
            $table->boolean('is_active')->default(true)->comment('Status aktif resort (true/false)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resorts');
    }
};

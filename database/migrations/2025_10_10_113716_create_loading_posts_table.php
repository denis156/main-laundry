<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loading_posts', function (Blueprint $table) {
            $table->id()->comment('ID unik pos loading');
            $table->string('name')->comment('Nama pos (Pos Jakarta Selatan)');
            $table->text('address')->comment('Alamat lengkap pos');
            $table->string('phone')->comment('Nomor telepon pos');
            $table->string('pic_name')->comment('Nama penanggung jawab pos');
            $table->json('area_coverage')->nullable()->comment('Area yang dilayani (JSON array)');
            $table->boolean('is_active')->default(true)->comment('Status aktif pos (true/false)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loading_posts');
    }
};

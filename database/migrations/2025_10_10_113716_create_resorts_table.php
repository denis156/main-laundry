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

            // Wilayah (Kota Kendari, Sulawesi Tenggara)
            $table->string('district_code')->nullable()->comment('Kode kecamatan (dari API wilayah.id)');
            $table->string('district_name')->nullable()->comment('Nama kecamatan');
            $table->string('village_code')->nullable()->comment('Kode kelurahan (dari API wilayah.id)');
            $table->string('village_name')->nullable()->comment('Nama kelurahan');
            $table->text('detail_address')->nullable()->comment('Detail alamat (Jl, RT/RW, nomor rumah, patokan)');
            $table->text('address')->nullable()->comment('Alamat lengkap gabungan (untuk display & backward compatibility)');

            $table->string('phone')->comment('Nomor telepon resort');
            $table->string('pic_name')->comment('Nama penanggung jawab resort');
            $table->json('area')->nullable()->comment('Array kecamatan yang dilayani resort ini');
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

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
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id()->comment('ID unik tier membership');
            $table->string('name')->comment('Nama tier (Bronze, Silver, Gold, dll)');
            $table->string('slug')->unique()->comment('Slug tier (unik)');
            $table->integer('min_points')->comment('Minimum poin untuk mencapai tier ini');
            $table->decimal('discount_percentage', 5, 2)->comment('Persentase diskon tier (0.00 - 100.00)');
            $table->string('color')->nullable()->comment('Warna badge tier');
            $table->text('benefits')->nullable()->comment('Benefit tier (JSON array)');
            $table->boolean('is_active')->default(true)->comment('Status aktif tier (true/false)');
            $table->integer('sort_order')->default(0)->comment('Urutan tampilan tier');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};

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
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('brand');
            $table->string('serial_number')->nullable();
            $table->decimal('purchase_price', 15, 2);
            $table->date('purchase_date');
            $table->enum('status', ['baik', 'rusak', 'maintenance'])->default('baik');
            $table->date('last_maintenance_date')->nullable();
            $table->decimal('last_maintenance_cost', 15, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};

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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed']); // percentage or fixed amount
            $table->decimal('value', 10, 2); // percentage value or fixed amount
            $table->decimal('min_transaction', 10, 2)->default(0); // minimum transaction to use promo
            $table->decimal('max_discount', 10, 2)->nullable(); // max discount for percentage type
            $table->integer('usage_limit')->nullable(); // total usage limit
            $table->integer('usage_count')->default(0); // current usage count
            $table->integer('usage_per_user')->nullable(); // limit per user/customer
            $table->datetime('valid_from');
            $table->datetime('valid_until');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};

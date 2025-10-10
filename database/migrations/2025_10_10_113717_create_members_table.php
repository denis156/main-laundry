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
        Schema::create('members', function (Blueprint $table) {
            $table->id()->comment('ID unik member');
            $table->foreignId('customer_id')->unique()->constrained()->cascadeOnDelete()->comment('ID customer (unik, cascade on delete)');
            $table->foreignId('membership_tier_id')->nullable()->constrained()->nullOnDelete()->comment('ID tier membership (null on delete)');
            $table->string('member_number')->unique()->comment('Nomor member (unik, format: MBR-YYYYMMDD-XXXX)');
            $table->date('member_since')->comment('Tanggal bergabung sebagai member');
            $table->integer('total_points')->default(0)->comment('Total poin yang dimiliki member');
            $table->boolean('is_active')->default(true)->comment('Status aktif member (true/false)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};

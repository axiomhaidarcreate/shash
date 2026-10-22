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
        Schema::create('loyalty_offers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم العرض
            $table->integer('points_per_visit')->default(0);
            $table->integer('points_per_order')->default(0);
            $table->integer('points_per_riyal')->default(0);
            $table->integer('required_points')->default(0); // النقاط المطلوبة
            $table->string('reward_description')->nullable(); // وصف المكافأة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_offers');
    }
};

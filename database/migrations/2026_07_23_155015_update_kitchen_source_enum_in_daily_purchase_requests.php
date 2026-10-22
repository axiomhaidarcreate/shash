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
        Schema::table('daily_purchase_requests', function (Blueprint $table) {
        DB::statement("ALTER TABLE daily_purchase_requests MODIFY COLUMN kitchen_source ENUM('شيشة', 'باريستا', 'مطعم', 'استقبال', 'تحايا') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_purchase_requests', function (Blueprint $table) {
        DB::statement("ALTER TABLE daily_purchase_requests MODIFY COLUMN kitchen_source ENUM('شيشة', 'باريستا', 'مطعم') NOT NULL");
        });
    }
};

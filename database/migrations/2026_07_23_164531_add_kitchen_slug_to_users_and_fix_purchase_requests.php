<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) إضافة kitchen_slug للمستخدمين
        Schema::table('users', function (Blueprint $table) {
            $table->string('kitchen_slug')->nullable()->after('email');
        });

        // 2) تحويل kitchen_source من ENUM إلى VARCHAR
        DB::statement("ALTER TABLE daily_purchase_requests MODIFY COLUMN kitchen_source VARCHAR(255) DEFAULT 'الشيشة'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kitchen_slug');
        });

        DB::statement("ALTER TABLE daily_purchase_requests MODIFY COLUMN kitchen_source ENUM('الشيشة','الباريستا','المطعم','استقبال','تحايا') DEFAULT 'الشيشة'");
    }
};

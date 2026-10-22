<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('target_kitchen')->default('shisha')->after('name');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('target_kitchen')->default('shisha')->after('batch_id');
        });

        // Set automatic routing based on existing categories
        DB::table('categories')->where('name', 'مشروبات')->update(['target_kitchen' => 'barista']);
        DB::table('categories')->where('name', 'مأكولات')->update(['target_kitchen' => 'restaurant']);
        // 'معسل' and others will default to 'shisha' unless changed manually.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('target_kitchen');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('target_kitchen');
        });
    }
};

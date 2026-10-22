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
        Schema::table('physical_inventory_items', function (Blueprint $table) {
            $table->decimal('system_daily_consumed', 12, 2)->default(0)->after('previous_quantity');
            $table->decimal('actual_daily_consumed', 12, 2)->default(0)->after('system_daily_consumed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('physical_inventory_items', function (Blueprint $table) {
            $table->dropColumn(['system_daily_consumed', 'actual_daily_consumed']);
        });
    }
};

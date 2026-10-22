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
        Schema::table('greeting_daily_reports', function (Blueprint $table) {
            $table->timestamp('opened_at')->nullable()->after('guest_details');
        });

        Schema::table('inventory_daily_reports', function (Blueprint $table) {
            $table->timestamp('opened_at')->nullable()->after('total_waste');
            $table->timestamp('closed_at')->nullable()->after('opened_at');
        });

        Schema::table('ticket_daily_reports', function (Blueprint $table) {
            $table->timestamp('opened_at')->nullable()->after('cancelled_tickets');
            $table->timestamp('closed_at')->nullable()->after('opened_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('greeting_daily_reports', function (Blueprint $table) {
            $table->dropColumn('opened_at');
        });

        Schema::table('inventory_daily_reports', function (Blueprint $table) {
            $table->dropColumn('opened_at');
            $table->dropColumn('closed_at');
        });

        Schema::table('ticket_daily_reports', function (Blueprint $table) {
            $table->dropColumn('opened_at');
            $table->dropColumn('closed_at');
        });
    }
};

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
            $table->boolean('is_admin_confirmed')->default(false)->after('notes');
            $table->timestamp('admin_confirmed_at')->nullable()->after('is_admin_confirmed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_purchase_requests', function (Blueprint $table) {
            $table->dropColumn(['is_admin_confirmed', 'admin_confirmed_at']);
        });
    }
};

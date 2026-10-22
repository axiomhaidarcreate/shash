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
        Schema::table('greeting_quotas', function (Blueprint $table) {
            $table->boolean('is_daily_closed')->default(false)->after('quantity');
        });

        Schema::table('guest_greetings', function (Blueprint $table) {
            $table->boolean('is_daily_closed')->default(false)->after('is_closed');
            $table->decimal('price', 10, 2)->nullable()->after('total_greetings');
        });
    }

    public function down(): void
    {
        Schema::table('greeting_quotas', function (Blueprint $table) {
            $table->dropColumn('is_daily_closed');
        });

        Schema::table('guest_greetings', function (Blueprint $table) {
            $table->dropColumn(['is_daily_closed', 'price']);
        });
    }
};

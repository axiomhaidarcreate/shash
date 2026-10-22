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
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('subscription_package_id')->nullable()->constrained()->nullOnDelete();
            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();
            $table->date('last_subscription_usage_date')->nullable();
            $table->integer('daily_subscription_usage_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['subscription_package_id']);
            $table->dropColumn([
                'subscription_package_id',
                'subscription_start_date',
                'subscription_end_date',
                'last_subscription_usage_date',
                'daily_subscription_usage_count',
            ]);
        });
    }
};

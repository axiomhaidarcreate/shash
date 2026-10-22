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
        Schema::table('tickets', function (Blueprint $table) {
            // كم مرة استُخدمت التذكرة
            $table->unsignedInteger('times_used')->default(0)->after('status');
            // أقصى عدد مرات مسموح باستخدام التذكرة (يُأخذ من القالب عند الإصدار)
            $table->unsignedInteger('max_uses')->default(1)->after('times_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['times_used', 'max_uses']);
        });
    }
};

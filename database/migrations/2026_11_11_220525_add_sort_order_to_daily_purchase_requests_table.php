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
        Schema::table('daily_purchase_requests', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('id');
        });

        // Initialize existing rows
        $items = DB::table('daily_purchase_requests')->orderBy('id', 'desc')->get();
        foreach ($items as $index => $item) {
            DB::table('daily_purchase_requests')
                ->where('id', $item->id)
                ->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_purchase_requests', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};

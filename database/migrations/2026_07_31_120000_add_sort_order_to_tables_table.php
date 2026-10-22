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
        Schema::table('tables', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('section');
        });

        // Set initial sort_order based on existing IDs
        $tables = \App\Models\Table::orderBy('id')->get();
        foreach ($tables as $index => $t) {
            $t->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};

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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->string('contact_number')->nullable();
            $table->decimal('cost_amount', 10, 2)->nullable();
            $table->date('execution_date')->nullable();
            $table->time('execution_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['location', 'contact_number', 'cost_amount', 'execution_date', 'execution_time']);
        });
    }
};

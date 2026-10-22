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
        Schema::create('greeting_daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->integer('opening_balance');
            $table->integer('used_balance');
            $table->integer('remaining_balance');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->json('guest_details')->nullable();
            $table->timestamp('closed_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('greeting_daily_reports');
    }
};

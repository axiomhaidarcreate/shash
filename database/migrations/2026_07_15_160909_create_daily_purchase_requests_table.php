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
        Schema::create('daily_purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->integer('quantity');
            $table->string('weight_or_ml')->nullable();
            $table->enum('importance', ['عاجل', 'متوسط', 'عادي'])->default('عادي');
            $table->string('attachment_path')->nullable();
            $table->date('needed_date');
            $table->enum('kitchen_source', ['شيشة', 'باريستا', 'مطعم']);
            $table->enum('status', ['معلق', 'تم الشراء'])->default('معلق');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_purchase_requests');
    }
};

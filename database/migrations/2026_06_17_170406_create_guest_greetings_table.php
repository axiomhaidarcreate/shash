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
        Schema::create('guest_greetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->string('guest_name');
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('total_greetings')->default(1);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_greetings');
    }
};

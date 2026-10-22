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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Receptionist who issued the ticket
            $table->string('guest_name')->nullable();
            $table->integer('num_people')->default(1);
            $table->string('barcode')->unique();
            $table->enum('status', ['active', 'used', 'cancelled'])->default('active');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete(); // Link to order once used
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

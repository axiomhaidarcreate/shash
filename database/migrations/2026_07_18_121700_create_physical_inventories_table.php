<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->string('status')->default('draft'); // draft, completed, approved
            $table->timestamps();
        });

        Schema::create('physical_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('physical_inventory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('unit')->nullable();
            $table->decimal('previous_quantity', 10, 2)->default(0); // System quantity at the time of inventory
            $table->decimal('current_quantity', 10, 2)->default(0);  // Physical quantity counted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_inventory_items');
        Schema::dropIfExists('physical_inventories');
    }
};

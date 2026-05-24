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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        // Seed with default categories to support existing products
        DB::table('categories')->insert([
            ['name' => 'معسل', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مشروبات', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مستلزمات', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'أخرى', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

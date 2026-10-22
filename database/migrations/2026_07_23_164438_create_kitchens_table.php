<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kitchens', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // الاسم بالعربي مثل "مطبخ الشيشة"
            $table->string('slug')->unique(); // المعرف الفريد مثل "shisha"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // إدراج المطابخ الموجودة مسبقاً
        DB::table('kitchens')->insert([
            ['name' => 'مطبخ الشيشة',   'slug' => 'shisha',     'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مطبخ الباريستا', 'slug' => 'barista',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مطبخ المطعم',    'slug' => 'restaurant', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchens');
    }
};

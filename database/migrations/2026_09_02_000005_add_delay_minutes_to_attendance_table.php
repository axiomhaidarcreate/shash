<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // هذا الـ migration كان يضيف أعمدة لجدول attendance في Supabase
        // تم دمج هذه الأعمدة مباشرةً في 2026_01_01_000000_create_base_tables.php
        // لذلك هذا الملف أصبح فارغاً لتجنب الأخطاء
    }

    public function down(): void
    {
        // لا يوجد شيء لحذفه
    }
};

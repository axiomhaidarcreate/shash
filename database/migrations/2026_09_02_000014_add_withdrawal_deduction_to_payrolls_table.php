<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // عمود withdrawal_deduction تم إضافته مباشرة في 2026_01_01_000000_create_base_tables.php
        // لذلك هذا الملف أصبح فارغاً لتجنب الأخطاء
        if (Schema::hasTable('payroll') && !Schema::hasColumn('payroll', 'withdrawal_deduction')) {
            Schema::table('payroll', function (Blueprint $table) {
                $table->decimal('withdrawal_deduction', 10, 2)->default(0)->after('other_deductions');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payroll') && Schema::hasColumn('payroll', 'withdrawal_deduction')) {
            Schema::table('payroll', function (Blueprint $table) {
                $table->dropColumn('withdrawal_deduction');
            });
        }
    }
};

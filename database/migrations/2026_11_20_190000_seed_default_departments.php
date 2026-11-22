<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use App\Models\Department;

return new class extends Migration
{
    public function up(): void
    {
        $departments = [
            ['name' => 'Management', 'name_ar' => 'الإدارة العامة'],
            ['name' => 'Shisha Kitchen', 'name_ar' => 'مطبخ الشيشة'],
            ['name' => 'Barista Kitchen', 'name_ar' => 'مطبخ الباريستا'],
            ['name' => 'Restaurant Kitchen', 'name_ar' => 'مطبخ المطعم'],
            ['name' => 'Reception', 'name_ar' => 'الاستقبال'],
            ['name' => 'Orders and Delivery', 'name_ar' => 'التوصيل والطلبات'],
            ['name' => 'Purchases and Warehouse', 'name_ar' => 'المشتريات والمستودع'],
            ['name' => 'Desserts', 'name_ar' => 'حلويات'],
            ['name' => 'Housekeeping and Cleaning', 'name_ar' => 'النظافة والخدمات'],
            ['name' => 'Tahaya', 'name_ar' => 'تحايا'],
            ['name' => 'Accounting and Finance', 'name_ar' => 'المحاسبة والمالية'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['name_ar' => $dept['name_ar']],
                [
                    'name' => $dept['name'],
                    'is_active' => true,
                ]
            );
        }

        \Illuminate\Support\Facades\Cache::forget('departments_all');
    }

    public function down(): void
    {
        // Don't delete departments on rollback to avoid cascade issues
    }
};


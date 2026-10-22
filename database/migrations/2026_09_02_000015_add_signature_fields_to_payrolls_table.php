<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll', 'receipt_url')) {
                $table->text('receipt_url')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('payroll', 'employee_signature_url')) {
                $table->text('employee_signature_url')->nullable()->after('receipt_url');
            }
            if (!Schema::hasColumn('payroll', 'is_face_id_verified')) {
                $table->boolean('is_face_id_verified')->default(false)->after('employee_signature_url');
            }
            if (!Schema::hasColumn('payroll', 'employee_received_at')) {
                $table->timestamp('employee_received_at')->nullable()->after('is_face_id_verified');
            }
            if (!Schema::hasColumn('payroll', 'manager_receipt_approved_at')) {
                $table->timestamp('manager_receipt_approved_at')->nullable()->after('employee_received_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payroll', function (Blueprint $table) {
            $table->dropColumn([
                'receipt_url',
                'employee_signature_url',
                'is_face_id_verified',
                'employee_received_at',
                'manager_receipt_approved_at',
            ]);
        });
    }
};

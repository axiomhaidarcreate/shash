<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // === MySQL Tables (Default Connection) ===

        // 1. Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('name_ar');
            $table->text('description')->nullable();
            $table->uuid('manager_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Employees
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable(); // Firebase Auth UID
            $table->string('employee_code')->unique();
            $table->string('password')->nullable();
            $table->string('full_name')->nullable();
            $table->string('full_name_ar');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('national_id')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->uuid('department_id')->nullable();
            $table->string('job_title')->nullable();
            $table->string('job_title_ar')->nullable();
            $table->string('employment_type')->nullable();
            $table->uuid('direct_manager_id')->nullable();
            $table->decimal('base_salary', 10, 2)->default(0);
            $table->string('bank_account')->nullable();
            $table->text('address')->nullable();
            $table->string('avatar_url')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Payroll
        Schema::create('payroll', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->integer('pay_period_year');
            $table->integer('pay_period_month');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('housing_allowance', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('other_allowances', 10, 2)->default(0);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->decimal('bonuses', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->decimal('social_insurance', 10, 2)->default(0);
            $table->decimal('tax_deduction', 10, 2)->default(0);
            $table->decimal('absent_deduction', 10, 2)->default(0);
            $table->decimal('withdrawal_deduction', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->integer('working_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->string('status')->default('draft');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('manager_receipt_approved_at')->nullable();
            $table->string('employee_signature_url')->nullable();
            $table->boolean('is_face_id_verified')->default(false);
            $table->timestamp('employee_received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });

        // 4. Leave Requests
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->string('leave_type');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
            $table->uuid('reviewer_id')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('attachment_url')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });

        

        // 6. Notifications (MySQL - was Supabase)
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('title')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('body')->nullable();
            $table->text('body_ar')->nullable();
            $table->string('type')->default('info');
            $table->boolean('is_read')->default(false);
            $table->string('related_id')->nullable();
            $table->string('related_table')->nullable();
            $table->string('action_url')->nullable();
            $table->timestamps();
        });

        // 7. Attendance (MySQL - was Supabase)
        Schema::create('attendance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->date('work_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('delay_minutes')->default(0);
            $table->integer('early_leave_minutes')->default(0);
            $table->decimal('check_in_lat', 10, 8)->nullable();
            $table->decimal('check_in_lng', 11, 8)->nullable();
            $table->decimal('check_out_lat', 10, 8)->nullable();
            $table->decimal('check_out_lng', 11, 8)->nullable();
            $table->string('status')->default('present');
            $table->decimal('overtime_hours', 5, 2)->nullable();
            $table->boolean('is_deducted')->default(false);
            $table->decimal('deduction_amount', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });

        // 8. Announcements (MySQL - was Supabase)
        Schema::create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('body')->nullable();
            $table->text('body_ar')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->uuid('published_by')->nullable();
            $table->string('target_role')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('notifications');
        
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('payroll');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('departments');
    }
};


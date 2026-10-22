<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Task Categories
        Schema::create('task_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // 2. Tasks
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->uuid('category_id')->nullable();
            $table->uuid('employee_id'); // Assigned to
            $table->uuid('department_id')->nullable(); // Optional: if assigned to a department pool
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Admin or manager who created it
            
            $table->string('title');
            $table->text('description')->nullable();
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            
            $table->date('due_date')->nullable();
            $table->time('due_time')->nullable();
            
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('task_categories')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });

        // 3. Task Comments
        Schema::create('task_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id');
            // Can be admin (user) or employee
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); 
            $table->uuid('employee_id')->nullable(); 
            $table->text('comment');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        // 4. Task Attachments
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id');
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->uuid('uploaded_by_employee_id')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('uploaded_by_employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_categories');
    }
};

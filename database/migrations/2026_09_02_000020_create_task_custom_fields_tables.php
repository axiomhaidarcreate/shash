<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Task Custom Fields - defines what fields are available
        Schema::create('task_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('label');           // Arabic label shown to user
            $table->string('name')->unique();  // machine-friendly key
            $table->enum('type', ['text', 'number', 'date', 'time', 'select', 'textarea'])->default('text');
            $table->text('options')->nullable(); // JSON options for 'select' type
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Task Custom Field Values - stores the actual values per task
        Schema::create('task_custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->uuid('task_id');
            $table->unsignedBigInteger('field_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('field_id')->references('id')->on('task_custom_fields')->onDelete('cascade');
            $table->unique(['task_id', 'field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_custom_field_values');
        Schema::dropIfExists('task_custom_fields');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custody_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custody_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('assigned_at');
            $table->date('returned_at')->nullable();
            
            $table->text('assignment_report')->nullable();
            $table->text('return_notes')->nullable();
            
            $table->json('assignment_attachments')->nullable();
            $table->json('return_attachments')->nullable();
            
            $table->longText('assignment_signature')->nullable(); // Can be base64 image or path
            $table->longText('return_signature')->nullable();     // Can be base64 image or path
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custody_assignments');
    }
};

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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE task_custom_fields MODIFY COLUMN type ENUM('text', 'number', 'date', 'time', 'select', 'textarea', 'file') DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE task_custom_fields MODIFY COLUMN type ENUM('text', 'number', 'date', 'time', 'select', 'textarea') DEFAULT 'text'");
    }
};

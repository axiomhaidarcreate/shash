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
        Schema::create('scheduled_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('message_templates')->nullOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('target_type')->default('all'); // all, nationality, event, subscribed, loyalty
            $table->string('target_value')->nullable(); // nationality name, event_id, etc.
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->default('pending'); // pending, sent, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_messages');
    }
};

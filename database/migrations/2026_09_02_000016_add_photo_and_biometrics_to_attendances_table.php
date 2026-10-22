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
        Schema::table('attendance', function (Blueprint $table) {
            $table->string('check_in_photo_url')->nullable();
            $table->string('check_out_photo_url')->nullable();
            $table->boolean('is_fingerprint_verified')->default(false);
            $table->boolean('is_face_id_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_photo_url',
                'check_out_photo_url',
                'is_fingerprint_verified',
                'is_face_id_verified'
            ]);
        });
    }
};

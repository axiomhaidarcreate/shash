<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artist_bookings', function (Blueprint $table) {
            $table->string('status')->default('suggested')->after('notes'); // approved or suggested
        });
    }

    public function down(): void
    {
        Schema::table('artist_bookings', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

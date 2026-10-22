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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('trade_name')->nullable()->after('company');
            $table->string('contact_person')->nullable()->after('trade_name');
            $table->string('tax_number')->nullable()->after('phone');
            $table->string('national_id')->nullable()->after('tax_number');
            $table->string('city')->nullable()->after('national_id');
            $table->string('address')->nullable()->after('city');
            $table->string('website')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'trade_name', 'contact_person', 'tax_number', 
                'national_id', 'city', 'address', 'website'
            ]);
        });
    }
};

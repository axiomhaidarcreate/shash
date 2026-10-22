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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('national_id_attachment')->nullable();
            $table->date('national_id_expiry')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('passport_attachment')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('health_cert_attachment')->nullable();
            $table->string('previous_sponsor')->nullable();
            $table->string('phone_2')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('housing_allowance', 8, 2)->nullable();
            $table->string('address_link')->nullable();
            $table->string('insurance_name')->nullable();
            $table->date('insurance_start_date')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->string('contract_attachment')->nullable();
            $table->string('resignation_attachment')->nullable();
            $table->integer('working_days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'national_id_attachment',
                'national_id_expiry',
                'passport_number',
                'passport_attachment',
                'passport_expiry',
                'health_cert_attachment',
                'previous_sponsor',
                'phone_2',
                'percentage',
                'housing_allowance',
                'address_link',
                'insurance_name',
                'insurance_start_date',
                'insurance_expiry_date',
                'contract_attachment',
                'resignation_attachment',
                'working_days',
            ]);
        });
    }
};

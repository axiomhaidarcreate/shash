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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->string('payment_type')->default('cash')->after('total_amount'); // cash, installment
            $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_type');
            $table->decimal('remaining_amount', 10, 2)->default(0)->after('paid_amount');
            $table->string('payment_status')->default('unpaid')->after('remaining_amount'); // unpaid, partial, paid
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'paid_amount', 'remaining_amount', 'payment_status']);
        });
    }
};

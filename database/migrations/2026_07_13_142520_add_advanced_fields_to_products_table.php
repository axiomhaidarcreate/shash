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
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->decimal('price', 8, 2)->default(0)->after('name_en');
            $table->string('image')->nullable()->after('price');
            $table->string('unit')->default('حبة')->after('image');
            $table->boolean('deduct_from_inventory')->default(true)->after('unit');
            $table->boolean('is_composite')->default(false)->after('deduct_from_inventory');
            $table->string('sku')->nullable()->unique()->after('is_composite');
            $table->decimal('tax_percentage', 5, 2)->default(15)->after('sku');
            $table->string('target_kitchen')->default('shisha')->after('tax_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'name_en', 'price', 'image', 'unit', 'deduct_from_inventory',
                'is_composite', 'sku', 'tax_percentage', 'target_kitchen'
            ]);
        });
    }
};

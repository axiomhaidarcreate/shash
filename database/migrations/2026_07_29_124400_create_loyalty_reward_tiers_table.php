<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loyalty_reward_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loyalty_offer_id')->constrained('loyalty_offers')->onDelete('cascade');
            $table->integer('required_points')->default(0); // النقاط المطلوبة لهذا المستوى
            $table->string('reward_description'); // وصف المكافأة
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->timestamps();
        });

        // Migrate existing data from loyalty_offers to the new tiers table
        $offers = DB::table('loyalty_offers')
            ->whereNotNull('reward_description')
            ->where('reward_description', '!=', '')
            ->get();

        foreach ($offers as $offer) {
            DB::table('loyalty_reward_tiers')->insert([
                'loyalty_offer_id' => $offer->id,
                'required_points' => $offer->required_points ?? 0,
                'reward_description' => $offer->reward_description,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_reward_tiers');
    }
};

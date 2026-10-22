<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTransaction;
use App\Models\LoyaltyOffer;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function index(Request $request)
    {
        $customer = $request->user();
        $transactions = LoyaltyTransaction::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPoints = $customer->total_points ?? 0;
        $usedPoints = $customer->used_points ?? 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_points' => $totalPoints,
                'used_points' => $usedPoints,
                'balance' => $totalPoints - $usedPoints,
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * بيانات الولاء التفصيلية مع المكافآت
     */
    public function details(Request $request)
    {
        $customer = $request->user();
        $customer->load('loyaltyOffer.rewardTiers');

        $transactions = LoyaltyTransaction::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        $totalPoints = $customer->total_points ?? 0;
        $usedPoints = $customer->used_points ?? 0;
        $balance = $totalPoints - $usedPoints;

        // بيانات عرض الولاء والمكافآت
        $offer = null;
        $rewardTiers = [];
        $nextReward = null;

        if ($customer->loyaltyOffer) {
            $offer = [
                'id' => $customer->loyaltyOffer->id,
                'name' => $customer->loyaltyOffer->name,
                'points_per_visit' => $customer->loyaltyOffer->points_per_visit,
                'points_per_order' => $customer->loyaltyOffer->points_per_order,
                'points_per_riyal' => $customer->loyaltyOffer->points_per_riyal,
                'required_points' => $customer->loyaltyOffer->required_points,
                'reward_description' => $customer->loyaltyOffer->reward_description,
            ];

            $rewardTiers = $customer->loyaltyOffer->rewardTiers->map(function ($tier) {
                return [
                    'id' => $tier->id,
                    'required_points' => $tier->required_points,
                    'reward_description' => $tier->reward_description,
                ];
            })->toArray();

            // تحديد المكافأة القادمة
            foreach ($rewardTiers as $tier) {
                if ($tier['required_points'] > $balance) {
                    $nextReward = $tier;
                    break;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_points' => $customer->total_points ?? 0,
                'used_points' => $customer->used_points ?? 0,
                'balance' => $balance,
                'offer' => $offer,
                'reward_tiers' => $rewardTiers,
                'next_reward' => $nextReward,
                'transactions' => $transactions,
            ]
        ]);
    }
    public function offers()
    {
        $offers = LoyaltyOffer::all();
        return response()->json([
            'status' => 'success',
            'data' => [
                'offers' => $offers
            ]
        ]);
    }

    public function subscribe(Request $request, $id)
    {
        $offer = LoyaltyOffer::findOrFail($id);
        $customer = $request->user();

        $customer->loyalty_offer_id = $offer->id;
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم الاشتراك في عرض الولاء بنجاح!',
            'data' => [
                'offer_id' => $offer->id
            ]
        ]);
    }
}


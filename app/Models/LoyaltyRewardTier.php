<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyRewardTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_offer_id', 'required_points', 'reward_description', 'sort_order'
    ];

    public function loyaltyOffer()
    {
        return $this->belongsTo(LoyaltyOffer::class);
    }
}

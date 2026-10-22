<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'points_per_visit', 'points_per_order', 'points_per_riyal', 'required_points', 'reward_description'
    ];

    public function rewardTiers()
    {
        return $this->hasMany(LoyaltyRewardTier::class)->orderBy('required_points', 'asc');
    }
}

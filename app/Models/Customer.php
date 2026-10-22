<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasApiTokens, Notifiable, \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'name', 'phone', 'password', 'gender', 'nationality', 'is_phone_verified', 'subscription_number', 'loyalty_offer_id', 'total_points', 'used_points',
        'subscription_package_id', 'subscription_start_date', 'subscription_end_date', 'last_subscription_usage_date', 'daily_subscription_usage_count',
        'firebase_uid', 'fcm_token'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function loyaltyOffer()
    {
        return $this->belongsTo(LoyaltyOffer::class);
    }

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }

    public function eventBookings()
    {
        return $this->hasMany(EventBooking::class);
    }

    public function hasActiveSubscription(): bool
    {
        if (!$this->subscription_package_id || !$this->subscription_end_date) {
            return false;
        }
        return now()->startOfDay()->lte(\Carbon\Carbon::parse($this->subscription_end_date)->endOfDay());
    }

    public function canUseSubscriptionToday(): bool
    {
        if (!$this->hasActiveSubscription()) {
            return false;
        }

        // Check if daily limit reached
        if ($this->last_subscription_usage_date && \Carbon\Carbon::parse($this->last_subscription_usage_date)->isToday()) {
            $limit = $this->subscriptionPackage->daily_order_limit ?? 1;
            if ($this->daily_subscription_usage_count >= $limit) {
                return false;
            }
        }

        return true;
    }
}

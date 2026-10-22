<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'request_type',
        'subscription_package_id',
        'loyalty_offer_id',
        'status',
        'admin_notes',
    ];

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }

    public function loyaltyOffer()
    {
        return $this->belongsTo(LoyaltyOffer::class);
    }
}

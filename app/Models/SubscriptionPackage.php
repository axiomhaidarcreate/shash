<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'daily_order_limit',
        'included_products',
        'is_active',
    ];

    protected $casts = [
        'included_products' => 'array',
        'is_active' => 'boolean',
    ];
}

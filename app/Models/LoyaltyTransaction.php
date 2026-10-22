<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'customer_id', 'points_added', 'points_used', 'description'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

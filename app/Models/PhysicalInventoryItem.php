<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'physical_inventory_id',
        'product_id',
        'unit',
        'previous_quantity',
        'system_daily_consumed',
        'actual_daily_consumed',
        'current_quantity',
    ];

    public function physicalInventory()
    {
        return $this->belongsTo(PhysicalInventory::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

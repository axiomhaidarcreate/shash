<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients', 'product_id', 'ingredient_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function modifierGroups()
    {
        return $this->hasMany(ModifierGroup::class);
    }
}

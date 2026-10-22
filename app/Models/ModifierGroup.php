<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModifierGroup extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function modifiers()
    {
        return $this->hasMany(ProductModifier::class);
    }
}

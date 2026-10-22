<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModifier extends Model
{
    protected $guarded = [];

    public function modifierGroup()
    {
        return $this->belongsTo(ModifierGroup::class);
    }
}

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
}

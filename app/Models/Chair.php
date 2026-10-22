<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chair extends Model
{
    protected $guarded = [];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

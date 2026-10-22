<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GreetingQuota extends Model
{
    protected $guarded = [];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }}

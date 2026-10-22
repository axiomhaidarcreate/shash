<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestGreeting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }}

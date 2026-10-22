<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GreetingDailyReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'guest_details' => 'array',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}

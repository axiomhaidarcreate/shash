<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_done' => 'boolean',
        'reminder_date' => 'date',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $fillable = [
        'phone',
        'message',
        'status',
        'response',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketDailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_tickets',
        'sold_tickets',
        'cancelled_tickets',
        'opened_at',
        'closed_at'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

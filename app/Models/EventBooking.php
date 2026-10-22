<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'gender',
        'table_number',
        'table_id',
        'chair_id',
        'chair_number',
        'status',
        'admin_notes',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}

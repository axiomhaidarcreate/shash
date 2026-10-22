<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'event_time',
        'artist_image',
        'is_active',
        'is_ended',
        'is_hidden',
        'total_income',
        'other_expenses',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_active' => 'boolean',
        'is_ended' => 'boolean',
        'is_hidden' => 'boolean',
        'total_income' => 'float',
        'other_expenses' => 'float',
    ];

    public function bookings()
    {
        return $this->hasMany(EventBooking::class);
    }

    public function artistBookings()
    {
        return $this->hasMany(ArtistBooking::class);
    }
}

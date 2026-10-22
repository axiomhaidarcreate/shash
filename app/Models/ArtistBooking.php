<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'musician_id',
        'event_id',
        'booking_date',
        'price',
        'notes',
        'status'
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function musician()
    {
        return $this->belongsTo(Musician::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

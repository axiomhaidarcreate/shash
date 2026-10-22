<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $guarded = [];

    public function eventBookings()
    {
        return $this->hasMany(EventBooking::class);
    }

    public function chairs()
    {
        return $this->hasMany(Chair::class);
    }

    public function hasEmptyChairs()
    {
        return $this->chairs()->where('status', 'empty')->exists();
    }

    public function emptyChairs()
    {
        return $this->chairs()->where('status', 'empty');
    }
}

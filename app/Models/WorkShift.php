<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_auto_opened' => 'boolean',
        'is_auto_closed' => 'boolean',
    ];

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Returns the start time of the current active shift.
     * If no shift is open, returns a future date so that queries return 0.
     */
    public static function getActiveShiftStart()
    {
        $activeShift = self::where('status', 'open')->latest()->first();
        if ($activeShift) {
            return $activeShift->opened_at;
        }
        
        // If no active shift, return a future date to ensure counters are 0
        return now()->addCentury();
    }
}

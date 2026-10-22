<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Entitlement extends Model
{
    
    use HasUuids;

    protected $fillable = [
        'employee_id',
        'type',
        'title',
        'reason',
        'date',
        'time',
        'amount',
        'attachment',
        'receipt_number',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'amount' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}


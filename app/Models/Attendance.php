<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Attendance extends Model
{
    
    use HasUuids;


    protected $table = 'attendance';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'employee_id',
        'work_date',
        'check_in',
        'check_out',
        'check_in_lat',
        'check_in_lng',
        'check_out_lat',
        'check_out_lng',
        'status',
        'overtime_hours',
        'notes',
        'created_by',
        'delay_minutes',
        'early_leave_minutes',
        'is_deducted',
        'deduction_amount',
    ];

    protected $casts = [
        'work_date'      => 'date',
        'check_in'       => 'datetime:H:i:s',
        'check_out'      => 'datetime:H:i:s',
        'check_in_lat'   => 'decimal:8',
        'check_in_lng'   => 'decimal:8',
        'check_out_lat'  => 'decimal:8',
        'check_out_lng'  => 'decimal:8',
        'overtime_hours' => 'decimal:2',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('work_date', $date);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('work_date', $year)
                     ->whereMonth('work_date', $month);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}


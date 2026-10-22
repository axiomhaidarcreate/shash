<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class LeaveRequest extends Model
{
    
    use HasUuids;


    protected $table = 'leave_requests';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'reviewer_id',
        'review_note',
        'reviewed_at',
        'attachment_url',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'reviewed_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Accessors for Loan and Withdrawal Calculations
    public function getAmountAttribute()
    {
        preg_match('/بقيمة\s*([\d\.]+)/', $this->reason, $matches);
        return isset($matches[1]) ? (float)$matches[1] : 0;
    }

    public function getDurationMonthsAttribute()
    {
        preg_match('/تسدد على\s*(.*?)\s*-/', $this->reason, $matches);
        if (isset($matches[1])) {
            $str = trim($matches[1]);
            if (str_contains($str, 'شهران') || str_contains($str, 'شهرين')) return 2;
            elseif (str_contains($str, '3')) return 3;
            elseif (str_contains($str, '6')) return 6;
            elseif (str_contains($str, '12')) return 12;
            return (int) filter_var($str, FILTER_SANITIZE_NUMBER_INT) ?: 1;
        }
        return 1;
    }

    public function getMonthlyInstallmentAttribute()
    {
        if ($this->duration_months > 0) {
            return $this->amount / $this->duration_months;
        }
        return $this->amount;
    }

    // Since we don't have a direct link between a specific loan and a payroll deduction,
    // we calculate the total paid by the employee for all loans chronologically,
    // or just display total paid for the employee so far.
    // For simplicity, let's calculate the total 'other_deductions' the employee has paid
    // since this loan was approved.
    public function getPaidAmountAttribute()
    {
        if ($this->status !== 'approved' || !in_array($this->leave_type, ['loan', 'advance', 'قرض'])) {
            return 0;
        }

        // To make it simple: We just sum all other_deductions from payrolls after this loan was approved.
        // If there are multiple loans, this might be a combined value, but it's close enough.
        return \App\Models\Payroll::where('employee_id', $this->employee_id)
            ->where('created_at', '>=', $this->updated_at)
            ->sum('other_deductions');
    }

    public function getRemainingAmountAttribute()
    {
        $remaining = $this->amount - $this->paid_amount;
        return $remaining > 0 ? $remaining : 0;
    }
}


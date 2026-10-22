<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Payroll extends Model
{
    
    use HasUuids;

    protected $table = 'payroll';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'employee_id',
        'pay_period_year',
        'pay_period_month',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'other_allowances',
        'overtime_pay',
        'bonuses',
        'gross_salary',
        'social_insurance',
        'tax_deduction',
        'absent_deduction',
        'other_deductions',
        'net_salary',
        'working_days',
        'absent_days',
        'status',
        'approved_by',
        'approved_at',
        'paid_at',
        'notes',
        'receipt_url',
        'employee_signature_url',
        'is_face_id_verified',
        'employee_received_at',
        'manager_receipt_approved_at',
    ];

    protected $casts = [
        'pay_period_year'     => 'integer',
        'pay_period_month'    => 'integer',
        'basic_salary'        => 'decimal:2',
        'housing_allowance'   => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'other_allowances'    => 'decimal:2',
        'overtime_pay'        => 'decimal:2',
        'bonuses'             => 'decimal:2',
        'gross_salary'        => 'decimal:2',
        'social_insurance'    => 'decimal:2',
        'tax_deduction'       => 'decimal:2',
        'absent_deduction'    => 'decimal:2',
        'other_deductions'    => 'decimal:2',
        'net_salary'          => 'decimal:2',
        'working_days'        => 'integer',
        'absent_days'         => 'integer',
        'approved_at'         => 'datetime',
        'paid_at'             => 'datetime',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeForPeriod($query, int $year, int $month)
    {
        return $query->where('pay_period_year', $year)
                     ->where('pay_period_month', $month);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}


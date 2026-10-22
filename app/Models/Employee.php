<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Laravel\Sanctum\HasApiTokens;


class Employee extends Authenticatable
{
    
    use HasApiTokens, HasUuids;

    protected $table = 'employees';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'employee_code',
        'password',
        'full_name',
        'full_name_ar',
        'email',
        'phone',
        'national_id',
        'gender',
        'date_of_birth',
        'hire_date',
        'termination_date',
        'department_id',
        'job_title',
        'job_title_ar',
        'employment_type',
        'direct_manager_id',
        'base_salary',
        'bank_account',
        'address',
        'avatar_url',
        'is_active',
        'notes',
        'national_id_attachment',
        'national_id_expiry',
        'passport_number',
        'passport_attachment',
        'passport_expiry',
        'health_cert_attachment',
        'previous_sponsor',
        'phone_2',
        'percentage',
        'housing_allowance',
        'address_link',
        'insurance_name',
        'insurance_start_date',
        'insurance_expiry_date',
        'contract_attachment',
        'resignation_attachment',
        'working_days',
        'contract_template_id',
        'health_cert_expiry',
        'contract_expiry_date',
        'health_cert_number',
        'nationality',
        'daily_work_hours',
    ];

    protected $casts = [
        'date_of_birth'         => 'date',
        'hire_date'             => 'date',
        'termination_date'      => 'date',
        'national_id_expiry'    => 'date',
        'passport_expiry'       => 'date',
        'health_cert_expiry'    => 'date',
        'contract_expiry_date'  => 'date',
        'insurance_start_date'  => 'date',
        'insurance_expiry_date' => 'date',
        'is_active'             => 'boolean',
        'base_salary'           => 'decimal:2',
        'percentage'            => 'decimal:2',
        'housing_allowance'     => 'decimal:2',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
    ];

    /* ─── Relationships ─────────────────────────── */

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'direct_manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'direct_manager_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'employee_id');
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(Entitlement::class, 'employee_id');
    }

    /* ─── Scopes ────────────────────────────────── */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, string $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function contractTemplate(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'contract_template_id');
    }

    /**
     * Get the full URL for the avatar.
     */
    protected function avatarUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                if (!$value) {
                    return null;
                }
                
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }

                if (str_starts_with($value, '/storage/')) {
                    return url($value);
                }

                return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
            },
        );
    }
}


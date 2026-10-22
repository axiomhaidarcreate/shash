<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;


class WorkPeriod extends Model
{
    
    use HasUuids;


    protected $table = 'work_periods';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'name_ar',
        'start_time',
        'end_time',
        'grace_period_mins',
        'is_active',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'grace_period_mins' => 'integer',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'work_period_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}


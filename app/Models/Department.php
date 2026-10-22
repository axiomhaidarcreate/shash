<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Department extends Model
{
    
    use HasUuids;

    protected $table = 'departments';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}


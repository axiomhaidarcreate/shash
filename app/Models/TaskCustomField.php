<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TaskCustomField extends Model
{
    
    protected $fillable = [
        'label',
        'name',
        'type',
        'options',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'options' => 'array',
    ];

    public function values()
    {
        return $this->hasMany(TaskCustomFieldValue::class, 'field_id');
    }

    public static function activeFields()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }
}


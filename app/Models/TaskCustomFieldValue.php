<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TaskCustomFieldValue extends Model
{
    
    protected $fillable = [
        'task_id',
        'field_id',
        'value',
    ];

    public function field()
    {
        return $this->belongsTo(TaskCustomField::class, 'field_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}


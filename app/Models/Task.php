<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;


class Task extends Model
{
    
    use HasUuids;

    protected $fillable = [
        'parent_id',
        'category_id',
        'employee_id',
        'department_id',
        'created_by',
        'manual_employee_name',
        'title',
        'client_name',
        'description',
        'priority',
        'status',
        'due_date',
        'due_time',
        'completed_at',
        'location',
        'contact_number',
        'cost_amount',
        'execution_date',
        'execution_time',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'due_time' => 'datetime:H:i',
        'completed_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class, 'task_id');
    }

    public function customFieldValues()
    {
        return $this->hasMany(TaskCustomFieldValue::class, 'task_id');
    }
}


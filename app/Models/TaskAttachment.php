<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class TaskAttachment extends Model
{
    
    use HasUuids;

    protected $fillable = [
        'task_id',
        'uploaded_by_user_id',
        'uploaded_by_employee_id',
        'file_path',
        'file_name',
        'file_type',
    ];

    protected $appends = ['file_url'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }

    public function getFileUrlAttribute()
    {
        if (filter_var($this->file_path, FILTER_VALIDATE_URL)) {
            return $this->file_path;
        }
        
        if (str_starts_with($this->file_path, '/storage/')) {
            return url($this->file_path);
        }

        return Storage::disk('public')->url($this->file_path);
    }
}


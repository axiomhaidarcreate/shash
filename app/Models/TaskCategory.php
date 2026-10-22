<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;


class TaskCategory extends Model
{
    
    use HasUuids;

    protected $fillable = ['name', 'color'];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'category_id');
    }
}


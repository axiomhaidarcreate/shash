<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Custody extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'details',
        'attachments',
        'status',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function assignments()
    {
        return $this->hasMany(CustodyAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(CustodyAssignment::class)->whereNull('returned_at')->latest();
    }
}


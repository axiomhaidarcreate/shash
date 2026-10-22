<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CustodyAssignment extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'custody_id',
        'employee_id',
        'assigned_at',
        'returned_at',
        'assignment_report',
        'return_notes',
        'assignment_attachments',
        'return_attachments',
        'assignment_signature',
        'return_signature',
    ];

    protected $casts = [
        'assigned_at' => 'date',
        'returned_at' => 'date',
        'assignment_attachments' => 'array',
        'return_attachments' => 'array',
    ];

    public function custody()
    {
        return $this->belongsTo(Custody::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}


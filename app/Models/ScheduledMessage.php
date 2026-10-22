<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'title',
        'content',
        'target_type',
        'target_value',
        'scheduled_at',
        'sent_at',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(MessageTemplate::class, 'template_id');
    }
}

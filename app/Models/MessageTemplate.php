<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'attachment',
    ];

    public function scheduledMessages()
    {
        return $this->hasMany(ScheduledMessage::class, 'template_id');
    }
}

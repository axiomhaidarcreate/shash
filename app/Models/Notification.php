<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Notification extends Model
{
    
    use HasUuids;


    protected $table = 'notifications';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'title',
        'title_ar',
        'body',
        'body_ar',
        'type',
        'is_read',
        'related_id',
        'related_table',
        'action_url',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }
}


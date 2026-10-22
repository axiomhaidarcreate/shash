<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Announcement extends Model
{
    
    use HasUuids;


    protected $table = 'announcements';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'title_ar',
        'body',
        'body_ar',
        'is_pinned',
        'published_by',
        'target_role',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'is_pinned'    => 'boolean',
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}


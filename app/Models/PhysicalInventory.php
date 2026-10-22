<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PhysicalInventoryItem::class);
    }
}

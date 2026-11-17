<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'quantity',
        'weight_or_ml',
        'importance',
        'attachment_path',
        'needed_date',
        'kitchen_source',
        'status',
        'requested_by',
        'invoice_image',
        'price',
        'notes',
        'sort_order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->sort_order) {
                $minOrder = static::min('sort_order') ?? 0;
                $model->sort_order = $minOrder - 1;
            }
        });
    }
}

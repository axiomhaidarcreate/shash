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
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}

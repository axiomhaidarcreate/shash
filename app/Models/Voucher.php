<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Voucher extends Model
{
    
    use HasUuids;


    protected $table = 'vouchers';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'employee_id',
        'amount',
        'type',       // reward | loan | deduction | other
        'date',
        'reason',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'date'       => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function typeLabelAr(): string
    {
        return match($this->type) {
            'reward'    => 'مكافأة',
            'loan'      => 'سلفة / قرض',
            'deduction' => 'خصم',
            default     => 'أخرى',
        };
    }
}


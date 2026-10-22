<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'total_amount',
        'notes',
        'attachment',
        'status',
        'payment_type',
        'paid_amount',
        'remaining_amount',
        'payment_status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function installments()
    {
        return $this->hasMany(PurchaseInstallment::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class, 'purchase_invoice_id');
    }
}

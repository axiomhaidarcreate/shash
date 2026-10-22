<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInstallment extends Model
{
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }
}

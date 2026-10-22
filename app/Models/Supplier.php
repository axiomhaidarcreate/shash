<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'company',
        'balance',
        'trade_name',
        'contact_person',
        'tax_number',
        'national_id',
        'city',
        'address',
        'website',
    ];

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }
}

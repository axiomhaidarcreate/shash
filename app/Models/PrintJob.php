<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrintJob extends Model
{
    use HasFactory;

    protected $fillable = ['receipt_data', 'status', 'printer_type'];
}

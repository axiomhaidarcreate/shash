<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musician extends Model
{
    use HasFactory;

    protected $fillable = [
        'real_name',
        'stage_name',
        'nationality',
        'instrument',
        'license_number',
        'phone',
        'image',
    ];
}

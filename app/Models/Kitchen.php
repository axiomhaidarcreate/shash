<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kitchen extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    public function users()
    {
        return $this->hasMany(User::class, 'kitchen_slug', 'slug');
    }
}

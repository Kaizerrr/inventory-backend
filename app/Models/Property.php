<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['name', 'inputType'];

    // Define the relationship with Records
    public function records()
    {
        return $this->hasMany(Records::class);
    }
}

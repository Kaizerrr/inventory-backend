<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    protected $fillable = ['name', 'purchased_date', 'lifespan'];

    // Define the relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Define the relationship with Records
    public function records()
    {
        return $this->hasMany(Records::class);
    }
}

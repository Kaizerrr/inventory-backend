<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['category_name', 'lifespan'];

    // Define the relationship with Items
    public function items()
    {
        return $this->hasMany(Items::class);
    }
}

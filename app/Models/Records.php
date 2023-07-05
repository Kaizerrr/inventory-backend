<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Records extends Model
{
    protected $fillable = ['item_id', 'property_id', 'category_id', 'value'];

    // Define the relationship with Items
    public function item()
    {
        return $this->belongsTo(Items::class);
    }

    // Define the relationship with Property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Define the relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

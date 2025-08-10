<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventProduct extends Model
{
    protected $fillable = [
        'event_id',
        'product_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function restaurantProduct(){
        return $this->belongsTo(RestaurantProduct::class, 'product_id');
    }

    public function branchsProduct(){
        return $this->hasMany(EventProductBranch::class);
    }
}

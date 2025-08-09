<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'phone',
        'latitude',
        'longitude',
        'is_active',
        'city_id',
        'restaurant_id',
        'created_by',
        'updated_by',
    ];

    public function city(){
        return $this->belongsTo(City::class);
    }
}

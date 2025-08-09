<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'is_active',
        'restaurant_id',
        'created_by',
        'updated_by',
    ];

    public function restaurant(){
        return $this->belongsTo(Restaurant::class,'restaurant_id');
    }
}

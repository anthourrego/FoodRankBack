<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventProductBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_product_id',
        'restaurant_branch_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function branch(){
        return $this->belongsTo(RestaurantBranch::class, 'restaurant_branch_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'city_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the branch where the event takes place.
     */
    public function restaurantBranch()
    {
        return $this->belongsTo(RestaurantBranch::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    protected $fillable = [
        'event_product_id',
        'event_product_branch_id',
        'rating',
        'comment',
        'latitude',
        'longitude',
        'ip',
        'mac',
        'fingerprint_device',
        'is_active',
        'created_by',
        'updated_by',
    ];


}

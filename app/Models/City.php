<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'state_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function state(){
        return $this->belongsTo(State::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoeboxTime extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'overage'
    ];
}

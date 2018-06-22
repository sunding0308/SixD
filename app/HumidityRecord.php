<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HumidityRecord extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'type', 'date', 'time'
    ];
}

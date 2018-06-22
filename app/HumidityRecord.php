<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HumidityRecord extends Model
{
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'type', 'date', 'time'
    ];
}

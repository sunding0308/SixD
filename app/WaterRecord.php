<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WaterRecord extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'date', 'time', 'flow', 'total_flow'
    ];
}

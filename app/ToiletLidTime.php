<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToiletLidTime extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'used_time', 'remain_time'
    ];
}

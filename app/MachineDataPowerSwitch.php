<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineDataPowerSwitch extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $dates = ['enabled_at','disabled_at'];

}

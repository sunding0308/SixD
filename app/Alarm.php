<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'position_change_alarm', 'service_alarm_status', 'sterilization_alarm', 'filter_alarm', 'water_shortage_alarm',
        'filter_anti_counterfeiting_alarm', 'slave_mobile_alarm', 'dehumidification_tank_full_water_alarm', 'malfunction_code',
    ];
}

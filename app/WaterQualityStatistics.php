<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WaterQualityStatistics extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'raw_water_tds', 'pure_water_tds', 'salt_rejection_rate'
    ];
}

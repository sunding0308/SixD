<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BluetoothRecord extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'started_at', 'stoped_at', 'total_time'
    ];
}

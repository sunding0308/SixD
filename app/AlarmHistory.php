<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlarmHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id',
        'malfunction_code',
        'cleared',
        'created_at',
        'updated_at',
    ];
}

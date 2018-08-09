<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushRecord extends Model
{
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'type', 'pushed_at'
    ];
}

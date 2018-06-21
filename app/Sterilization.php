<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sterilization extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'uv1','uv2','uv3','uv4','uv5','uv6',
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'version_name', 'version_code', 'description', 'url'
    ];
}

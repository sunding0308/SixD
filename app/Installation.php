<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'hotel_name', 'hotel_code', 'hotel_address', 'room', 'machine_name', 'machine_model', 'installation_date', 'production_date'
    ];
}

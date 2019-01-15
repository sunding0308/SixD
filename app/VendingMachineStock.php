<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendingMachineStock extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'position', 'total_stock_in', 'total_stock_out', 'name', 'quantity', 'unit'
    ];

    //position
    const POSITION_ONE = 1;
    const POSITION_TWO = 2;
    const POSITION_THREE = 3;
    const POSITION_FOUR = 4;
    const POSITION_FIVE = 5;
    const POSITION_SIX = 6;
    const POSITION_SEVEN = 7;
    const POSITION_EIGHT = 8;

    const MAX_STOCK = 2;
}

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

    /**
     * Stock in with count, ignore if exceed max stock, update total stock in
     * @param $count
     */
    public function in($count)
    {
        if ($this->quantity + $count > self::MAX_STOCK) {
            $count = self::MAX_STOCK - $this->quantity;
        }
        $this->quantity += $count;
        $this->total_stock_in += $count;
    }

    /**
     * Stock out with count, ignore if out of stock, update total stock out
     * @param $count
     */
    public function out($count)
    {
        if ($this->quantity - $count < 0 ) {
            $count = $this->quantity;
        }
        $this->quantity -= $count;
        $this->total_stock_out += $count;
    }
}

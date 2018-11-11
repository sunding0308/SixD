<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class StockController extends ApiController
{
    public function stockIn(Request $request)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            foreach($request->products as $product) {
                $stock = $machine->stocks()->where('position', $product['position'])->first();
                $stock->quantity += $product['quantity'];
                $stock->save();
            }

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' stock in error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function stockOut(Request $request)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            foreach($request->products as $product) {
                $stock = $machine->stocks()->where('position', $product['position'])->first();
                $stock->quantity -= $product['quantity'];
                $stock->save();
            }

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' stock in error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

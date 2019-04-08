<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use App\VendingMachineStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class StockController extends ApiController
{
    public function stockIn(Request $request)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            if (!$machine) {
                return $this->responseNotFoundWithMessage('Machine not found');
            }
            foreach($request->products as $product) {
                $stock = $machine->stocks()->where('position', $product['pos'])->first();
                $count = $product['q'];
                if ($stock->quantity + $count > VendingMachineStock::MAX_STOCK) {
                    $count = VendingMachineStock::MAX_STOCK - $stock->quantity;
                }
                $stock->quantity += $count;
                $stock->total_stock_in += $count;
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
            if (!$machine) {
                return $this->responseNotFoundWithMessage('Machine not found');
            }
            foreach($request->products as $product) {
                $stock = $machine->stocks()->where('position', $product['pos'])->first();
                $count = $product['q'];
                if ($stock->quantity - $count < 0 ) {
                    $count = $stock->quantity;
                }

                $stock->quantity -= $count;
                $stock->total_stock_out += $count;
                $stock->save();
            }

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' stock in error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

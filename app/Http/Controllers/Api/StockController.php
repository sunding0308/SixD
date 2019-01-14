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
                $stock->quantity += $product['q'];
                $stock->total_stock_in += VendingMachineStock::MAX_STOCK - $stock->quantity;
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
                $stock->quantity -= $product['q'];
                $stock->total_stock_out += VendingMachineStock::MAX_STOCK - $stock->quantity;
                if ($stock->quantity >= 0) {
                    $stock->save();
                }
            }

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' stock in error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

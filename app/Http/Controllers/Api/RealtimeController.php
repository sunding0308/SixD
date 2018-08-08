<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;

class RealtimeController extends ApiController
{
    public function overage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }
            
            $machine = Machine::where('device',$request->device)->first();
            Machine::where('id',$machine->id)->update([
                'hot_water_overage' => $request->hot_water_overage,
                'cold_water_overage' => $request->cold_water_overage,
                'oxygen_overage' => $request->oxygen_overage,
                'air_overage' => $request->air_overage,
                'humidity_overage' => $request->humidity_overage,
            ]);

            Log::info('Device '.$request->device.' refresh overage success!');
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' refresh overage error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function heartbeat(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('device',$request->device)->first();
            $machine->touch();

            Log::info('Device '.$request->device.' heartbeat success!');
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' heartbeat error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

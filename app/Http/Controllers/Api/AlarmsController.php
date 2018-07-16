<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;

class AlarmsController extends ApiController
{
    public function alarms(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }
            
            $machine = Machine::with('alarm')->where('device',$request->device)->first();
            $alarm = $machine->alarm;
            if ($alarm) {
                $alarm->update([
                    'position_change_alarm' => $request->position_change_alarm ?: '',
                    'service_alarm_status' => $request->service_alarm_status ?: '',
                    'sterilization_alarm' => $request->sterilization_alarm ?: '',
                    'filter_alarm' => $request->filter_alarm ?: '',
                    'water_shortage_alarm' => $request->water_shortage_alarm ?: '',
                    'filter_anti_counterfeiting_alarm' => $request->filter_anti_counterfeiting_alarm ?: '',
                    'slave_mobile_alarm' => $request->slave_mobile_alarm ?: '',
                    'dehumidification_tank_full_water_alarm' => $request->dehumidification_tank_full_water_alarm ?: '',
                    'malfunction_code' => $request->malfunction_code ?: '',
                ]);

                Log::info('Device '.$request->device.' create alarm success!');
            } else {
                $machine->alarm()->create([
                    'machine_id' => $machine->id,
                    'position_change_alarm' => $request->position_change_alarm ?: '',
                    'service_alarm_status' => $request->service_alarm_status ?: '',
                    'sterilization_alarm' => $request->sterilization_alarm ?: '',
                    'filter_alarm' => $request->filter_alarm ?: '',
                    'water_shortage_alarm' => $request->water_shortage_alarm ?: '',
                    'filter_anti_counterfeiting_alarm' => $request->filter_anti_counterfeiting_alarm ?: '',
                    'slave_mobile_alarm' => $request->slave_mobile_alarm ?: '',
                    'dehumidification_tank_full_water_alarm' => $request->dehumidification_tank_full_water_alarm ?: '',
                    'malfunction_code' => $request->malfunction_code ?: '',
                ]);

                Log::info('Device '.$request->device.' update alarm success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' update alarm error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

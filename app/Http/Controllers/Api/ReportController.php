<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use App\PushRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;

class ReportController extends ApiController
{
    public function hardwareStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }
            
            $machine = Machine::where('device',$request->device)->first();
            $machine->update([
                'status' => $request->hardware_status['machine_status'] ?: $machine->status,
                'g_status' => $request->hardware_status['g_status'] ?: $machine->g_status,
                'wifi_status' => $request->hardware_status['wifi_status'] ?: $machine->wifi_status,
                'bluetooth_status' => $request->hardware_status['bluetooth_status'] ?: $machine->bluetooth_status,
                'filter1_lifespan' => $request->hardware_status['filter1_lifespan'] ?: $machine->filter1_lifespan,
                'filter2_lifespan' => $request->hardware_status['filter2_lifespan'] ?: $machine->filter2_lifespan,
                'filter3_lifespan' => $request->hardware_status['filter3_lifespan'] ?: $machine->filter3_lifespan,
                'total_produce_water_time' => $request->hardware_status['total_produce_water_time'] ?: $machine->total_produce_water_time,
            ]);

            $machine->sterilization()->update([
                'uv1' => $request->sterilization_time['uv1'] ?: $machine->sterilization->uv1,
                'uv2' => $request->sterilization_time['uv2'] ?: $machine->sterilization->uv2,
                'uv3' => $request->sterilization_time['uv3'] ?: $machine->sterilization->uv3,
                'uv4' => $request->sterilization_time['uv4'] ?: $machine->sterilization->uv4,
                'uv5' => $request->sterilization_time['uv5'] ?: $machine->sterilization->uv5,
                'uv6' => $request->sterilization_time['uv6'] ?: $machine->sterilization->uv6,
            ]);

            Log::info('Device '.$request->device.' update hardware status success!');
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' update hardware status error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
    
    public function records(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('device',$request->device)->first();
            $machine->waterRecords()->delete();
            $machine->waterRecords()->createMany($request->water_records);
            Log::info('Device '.$request->device.' update water records success!');

            $machine->oxygenRecords()->delete();
            $machine->oxygenRecords()->createMany($request->oxygen_records);
            Log::info('Device '.$request->device.' update oxygen records success!');

            $machine->airRecords()->delete();
            $machine->airRecords()->createMany($request->air_records);
            Log::info('Device '.$request->device.' update air records success!');

            $machine->humidityRecords()->delete();
            $machine->humidityRecords()->createMany($request->humidity_records);
            Log::info('Device '.$request->device.' update humidity records success!');
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' update records error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function environment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('device',$request->device)->first();
            $machine->update([
                'temperature' => $request->temperature ?: $machine->temperature,
                'humidity' => $request->humidity ?: $machine->humidity,
                'pm2_5' => $request->pm2_5 ?: $machine->pm2_5,
                'oxygen_concentration' => $request->oxygen_concentration ?: $machine->oxygen_concentration,
            ]);

            Log::info('Device '.$request->device.' update environment success!');
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' update environment error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function waterQualityStatistics(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('device',$request->device)->first();
            $machine->waterQualityStatistics()->create([
                'machine_id' => $machine->id,
                'raw_water_tds' => $request->raw_water_tds ?: 0,
                'pure_water_tds' => $request->pure_water_tds ?: 0,
                'salt_rejection_rate' => $request->salt_rejection_rate ?: 0,
            ]);

            Log::info('Device '.$request->device.' create water quality statistics success!');
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' create water quality statistics error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function appMenuAnalysis(Request $request)
    {
        return $request->data;
    }

    public function apiAnalysis(Request $request)
    {
        return $request->data;
    }
}

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
                'status' => $request->hardware_status['machine_status'] ?: '',
                'g_status' => $request->hardware_status['g_status'] ?: '',
                'wifi_status' => $request->hardware_status['wifi_status'] ?: '',
                'bluetooth_status' => $request->hardware_status['bluetooth_status'] ?: '',
                'filter1_lifespan' => $request->hardware_status['filter1_lifespan'] ?: '',
                'filter2_lifespan' => $request->hardware_status['filter2_lifespan'] ?: '',
                'filter3_lifespan' => $request->hardware_status['filter3_lifespan'] ?: '',
                'total_produce_water_time' => $request->hardware_status['total_produce_water_time'] ?: 0,
            ]);

            $machine->sterilization()->update([
                'uv1' => $request->sterilization_time['uv1'] ?: 0,
                'uv2' => $request->sterilization_time['uv2'] ?: 0,
                'uv3' => $request->sterilization_time['uv3'] ?: 0,
                'uv4' => $request->sterilization_time['uv4'] ?: 0,
                'uv5' => $request->sterilization_time['uv5'] ?: 0,
                'uv6' => $request->sterilization_time['uv6'] ?: 0,
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
                'temperature' => $request->temperature ?: 0,
                'humidity' => $request->humidity ?: 0,
                'pm2_5' => $request->pm2_5 ?: 0,
                'oxygen_concentration' => $request->oxygen_concentration ?: 0,
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

    public function pushReceived(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }
        Log::info('Device '.$request->device.' $request->type received!');
        
        $machine = Machine::where('device',$request->device)->first();
        $pushRecord = PushRecord::where('machine_id', $machine->id)
            ->where('type', $request->type)
            ->where('pushed_at', $request->pushed_at)
            ->first();

        if (!$pushRecord) {
            return $this->responseErrorWithMessage('无效的device！');
        }

        $pushRecord->delete();

        if ($request->type == 'topup') {
            Machine::where('id',$machine->id)->update([
                'hot_water_overage' => $request->overage[0],
                'cold_water_overage' => $request->overage[1],
                'oxygen_overage' => $request->overage[2],
                'air_overage' => $request->overage[3],
                'humidity_overage' => $request->overage[4]
            ]);
            Log::info('Device '.$machine->device.' topup success!');
        }

        return $this->responseSuccess();
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

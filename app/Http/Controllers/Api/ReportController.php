<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function hardwareStatus(Request $request)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            $machine->update([
                'status' => $request->hardware_status['machine_status'] ?: '',
                'g_status' => $request->hardware_status['g_status'] ?: '',
                'wifi_status' => $request->hardware_status['wifi_status'] ?: '',
                'bluetooth_status' => $request->hardware_status['bluetooth_status'] ?: '',
                'filter1_lifespan' => $request->hardware_status['filter1_lifespan'] ?: '',
                'filter2_lifespan' => $request->hardware_status['filter2_lifespan'] ?: '',
                'filter3_lifespan' => $request->hardware_status['filter3_lifespan'] ?: '',
                'total_produce_water_time' => $request->hardware_status['total_produce_water_time'] ?: '',
            ]);

            $machine->sterilization()->update([
                'uv1' => $request->sterilization_time['uv1'] ?: '',
                'uv2' => $request->sterilization_time['uv2'] ?: '',
                'uv3' => $request->sterilization_time['uv3'] ?: '',
                'uv4' => $request->sterilization_time['uv4'] ?: '',
                'uv5' => $request->sterilization_time['uv5'] ?: '',
                'uv6' => $request->sterilization_time['uv6'] ?: '',
            ]);

            Log::info('Device '.$request->device.' update hardware status success!');
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' update hardware status error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
    
    public function records(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' update records error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function environment(Request $request)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            $machine->update([
                'temperature' => $request->temperature ?: '',
                'humidity' => $request->humidity ?: '',
                'pm2_5' => $request->pm2_5 ?: '',
                'oxygen_concentration' => $request->oxygen_concentration ?: '',
            ]);

            Log::info('Device '.$request->device.' update environment success!');
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' update environment error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function waterQualityStatistics(Request $request)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            $machine->waterQualityStatistics()->create([
                'machine_id' => $machine->id,
                'raw_water_tds' => $request->raw_water_tds ?: '',
                'pure_water_tds' => $request->pure_water_tds ?: '',
                'salt_rejection_rate' => $request->salt_rejection_rate ?: '',
            ]);

            Log::info('Device '.$request->device.' create water quality statistics success!');
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

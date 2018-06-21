<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use App\Sterilization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class OnlineController extends Controller
{
    public function online(Request $request)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            if (!$machine) {
                $machine = Machine::create([
                    'device' => $request->device,
                    'registration_id' => $request->registration_id,
                    'status' => $request->hardware_status['machine_status'],
                    'g_status' => $request->hardware_status['g_status'],
                    'wifi_status' => $request->hardware_status['wifi_status'],
                    'bluetooth_status' => $request->hardware_status['bluetooth_status'],
                    'water_overage' => $request->overage['water_overage'],
                    'oxygen_overage' => $request->overage['oxygen_overage'],
                    'air_overage' => $request->overage['air_overage'],
                    'humidity_overage' => $request->overage['humidity_overage'],
                    'filter1_lifespan' => $request->hardware_status['filter1_lifespan'],
                    'filter2_lifespan' => $request->hardware_status['filter2_lifespan'],
                    'filter3_lifespan' => $request->hardware_status['filter3_lifespan'],
                    'temperature' => $request->environment['temperature'],
                    'humidity' => $request->environment['humidity'],
                    'pm2_5' => $request->environment['pm2_5'],
                    'oxygen_concentration' => $request->environment['oxygen_concentration'],
                    'total_produce_water_time' => $request->hardware_status['total_produce_water_time'],
                ]);
                Sterilization::create([
                    'machine_id' => $machine->id,
                    'uv1' => $request->hardware_status['sterilization_time']['uv1'],
                    'uv2' => $request->hardware_status['sterilization_time']['uv2'],
                    'uv3' => $request->hardware_status['sterilization_time']['uv3'],
                    'uv4' => $request->hardware_status['sterilization_time']['uv4'],
                    'uv5' => $request->hardware_status['sterilization_time']['uv5'],
                    'uv6' => $request->hardware_status['sterilization_time']['uv6'],
                ]);
            } else {
                Machine::where('id',$machine->id)->update([
                    'registration_id' => $request->registration_id,
                    'status' => $request->hardware_status['machine_status'],
                    'g_status' => $request->hardware_status['g_status'],
                    'wifi_status' => $request->hardware_status['wifi_status'],
                    'bluetooth_status' => $request->hardware_status['bluetooth_status'],
                    'water_overage' => $request->overage['water_overage'],
                    'oxygen_overage' => $request->overage['oxygen_overage'],
                    'air_overage' => $request->overage['air_overage'],
                    'humidity_overage' => $request->overage['humidity_overage'],
                    'filter1_lifespan' => $request->hardware_status['filter1_lifespan'],
                    'filter2_lifespan' => $request->hardware_status['filter2_lifespan'],
                    'filter3_lifespan' => $request->hardware_status['filter3_lifespan'],
                    'temperature' => $request->environment['temperature'],
                    'humidity' => $request->environment['humidity'],
                    'pm2_5' => $request->environment['pm2_5'],
                    'oxygen_concentration' => $request->environment['oxygen_concentration'],
                    'total_produce_water_time' => $request->hardware_status['total_produce_water_time'],
                ]);
                Sterilization::where('machine_id',$machine->id)->update([
                    'machine_id' => $machine->id,
                    'uv1' => $request->hardware_status['sterilization_time']['uv1'],
                    'uv2' => $request->hardware_status['sterilization_time']['uv2'],
                    'uv3' => $request->hardware_status['sterilization_time']['uv3'],
                    'uv4' => $request->hardware_status['sterilization_time']['uv4'],
                    'uv5' => $request->hardware_status['sterilization_time']['uv5'],
                    'uv6' => $request->hardware_status['sterilization_time']['uv6'],
                ]);
            }

            Log::info('Device '.$request->device.' online success!');
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' online error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

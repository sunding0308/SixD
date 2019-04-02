<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use App\Version;
use Carbon\Carbon;
use App\ShoeboxTime;
use App\Relenishment;
use App\Sterilization;
use App\ToiletLidTime;
use App\WashingMachineTime;
use App\Services\IotService;
use App\VendingMachineStock;
use Illuminate\Http\Request;
use App\Services\DubboProxyService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MachineInfo as MachineInfoResource;

class OnlineController extends ApiController
{
    //机器、用户排名信息
    const RANK_URL = 'com.sixdrops.outer.machinecloud.service.OuterMachineRankService';

    public function online(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required',
                'registration_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('device',$request->device)->first();
            Machine::where('id',$machine->id)->update([
                'registration_id' => $request->registration_id,
                'status' => $request->hardware_status['machine_status'] ?: '',
                'g_status' => $request->hardware_status['g_status'] ?: '',
                'wifi_status' => $request->hardware_status['wifi_status'] ?: '',
                'bluetooth_status' => $request->hardware_status['bluetooth_status'] ?: '',
                'hot_water_overage' => $request->overage['hot_water_overage'] ?? 0,
                'cold_water_overage' => $request->overage['cold_water_overage'] ?? 0,
                'oxygen_overage' => $request->overage['oxygen_overage'] ?? 0,
                'air_overage' => $request->overage['air_overage'] ?? 0,
                'humidity_add_overage' => $request->overage['humidity_add_overage'] ?? 0,
                'humidity_minus_overage' => $request->overage['humidity_minus_overage'] ?? 0,
                'humidity_child_overage' => $request->overage['humidity_child_overage'] ?? 0,
                'humidity_adult_overage' => $request->overage['humidity_adult_overage'] ?? 0,
                'filter1_lifespan' => $request->hardware_status['filter1_lifespan'] ?: '',
                'filter2_lifespan' => $request->hardware_status['filter2_lifespan'] ?: '',
                'filter3_lifespan' => $request->hardware_status['filter3_lifespan'] ?: '',
                'temperature' => $request->environment['temperature'] ?? 0,
                'humidity' => $request->environment['humidity'] ?? 0,
                'pm2_5' => $request->environment['pm2_5'] ?? 0,
                'oxygen_concentration' => $request->environment['oxygen_concentration'] ?? 0,
                'total_produce_water_time' => $request->hardware_status['total_produce_water_time'] ?? 0,
                'app_version' => $request->app_version ?? '',
                'firmware_version' => $request->firmware_version ?? ''
            ]);

            if ($machine->type == Machine::TYPE_WATER) {
                Sterilization::where('machine_id',$machine->id)->update([
                    'machine_id' => $machine->id,
                    'uv1' => $request->hardware_status['sterilization_time']['uv1'] ?? 0,
                    'uv2' => $request->hardware_status['sterilization_time']['uv2'] ?? 0,
                    'uv3' => $request->hardware_status['sterilization_time']['uv3'] ?? 0,
                    'uv4' => $request->hardware_status['sterilization_time']['uv4'] ?? 0,
                    'uv5' => $request->hardware_status['sterilization_time']['uv5'] ?? 0,
                    'uv6' => $request->hardware_status['sterilization_time']['uv6'] ?? 0
                ]);
            }

            Log::info('Device '.$request->device.' online success!');
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' online error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    /**
     * 暂时保留，机器已不调此接口
     */
    public function getUserRank(Request $request)
    {
        $machine = Machine::where('device',$request->device)->first();
        if (!$machine || !$machine->machine_id) {
            return $this->responseErrorWithMessage('设备未安装');
        }
        //获取VIP码产品信息
        $service = DubboProxyService::getService(self::RANK_URL, [
            'registry' => config('dubbo.registry'),
            'version' => config('dubbo.version')
        ]);
        $exchangeResult = $service->findMachineRank($machine->machine_id);

        return $this->responseSuccessWithExtrasAndMessage(['data' => $exchangeResult]);
    }

    public function getMachineInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();
        return new MachineInfoResource($machine);
    }

    public function register(Request $request, IotService $iot)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            if (!$machine) {
                $machine = Machine::create([
                    'device' => $request->device,
                    'type' => $request->type,
                    'machine_id' => '',
                    'registration_id' => $request->device,
                    'status' => '',
                    'g_status' => '',
                    'wifi_status' => '',
                    'bluetooth_status' => '',
                    'hot_water_overage' => 0,
                    'cold_water_overage' => 0,
                    'oxygen_overage' => 0,
                    'air_overage' => 0,
                    'humidity_add_overage' => 0,
                    'humidity_minus_overage' => 0,
                    'humidity_child_overage' => 0,
                    'humidity_adult_overage' => 0,
                    'filter1_lifespan' => '',
                    'filter2_lifespan' => '',
                    'filter3_lifespan' => '',
                    'temperature' => 0,
                    'humidity' => 0,
                    'pm2_5' => 0,
                    'oxygen_concentration' => 0,
                    'total_produce_water_time' => 0,
                    'app_version' => '',
                    'firmware_version' => ''
                ]);
                if ($machine->type == Machine::TYPE_WATER) {
                    Sterilization::create([
                        'machine_id' => $machine->id,
                        'uv1' => 0,
                        'uv2' => 0,
                        'uv3' => 0,
                        'uv4' => 0,
                        'uv5' => 0,
                        'uv6' => 0
                    ]);
                } else if ($machine->type == Machine::TYPE_VENDING) {
                    foreach(config('vending') as $val) {
                        array_push($val, $machine->id);
                        array_push($val, 0);
                        $this->createStock($val);
                    }
                } else if ($machine->type == Machine::TYPE_RELENISHMENT) {
                    foreach(range(1, 16) as $position) {
                        Relenishment::create([
                            'machine_id' => $machine->id,
                            'position' => $position,
                            'serial' => ''
                        ]);
                    }
                } else if($machine->type == Machine::TYPE_WASHING) {
                    WashingMachineTime::create([
                        'machine_id' => $machine->id,
                        'used_time' => 0,
                        'remain_time' => 0
                    ]);
                } else if($machine->type == Machine::TYPE_SHOEBOX) {
                    ShoeboxTime::create([
                        'machine_id' => $machine->id,
                        'used_time' => 0,
                        'remain_time' => 0
                    ]);
                } else if($machine->type == Machine::TYPE_TOILET_LID) {
                    ToiletLidTime::create([
                        'machine_id' => $machine->id,
                        'used_time' => 0,
                        'remain_time' => 0
                    ]);
                }
            }
            $response = $iot->registDevice($request->device);
            return $this->responseSuccessWithExtrasAndMessage($response);
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' register error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function logfile(Request $request)
    {
        try {
            $base_path = $request['device'] . '/'; //存放目录
            Storage::disk('public')->putFileAs($base_path, $request->file('file'), Carbon::now()->timestamp . '-' . $request->file('file')->getClientOriginalName());
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request['device'].' file upload error: '.$e->getMessage().' Line: '.$e->getLine());
            return $this->responseErrorWithMessage($e->getMessage());
        }
    }

    public function getOta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'versionName' => 'required',
            'versionCode' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $latestVersion = Version::latest()->first();
        if (!$latestVersion) {
            return $this->responseErrorWithMessage('版本库中还没有任何版本');
        }
        $latestVersionCode = $latestVersion->version_code;
        $versionCode = $request->versionCode;
        if ($versionCode >= $latestVersionCode) {
            return $this->responseErrorWithMessage('当前已是最新版本');
        }

        return $this->responseSuccessWithExtrasAndMessage([
            'data' => [
                'versionName' => $latestVersion->version_name,
                'versionCode' => $latestVersion->version_code,
                'description' => $latestVersion->description,
                'url' => config('app.url').'/api/version/download?url='.$latestVersion->url,
            ]
        ]);
    }

    public function versionDownload(Request $request)
    {
        return Storage::download($request->url);
    }

    private function createStock($val)
    {
        VendingMachineStock::create([
            'position' => $val[0],
            'name' => $val[1],
            'unit' => $val[2],
            'machine_id' => $val[3],
            'quantity' => $val[4],
            'total_stock_in' => 0,
            'total_stock_out' => 0,
        ]);
    }
}

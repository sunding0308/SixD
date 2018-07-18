<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use App\UserRank;
use App\Installation;
use App\Sterilization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserRankResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;

class OnlineController extends ApiController
{
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
            if (!$machine) {
                $machine = Machine::create([
                    'device' => $request->device,
                    'registration_id' => $request->registration_id,
                    'status' => $request->hardware_status['machine_status'] ?: '',
                    'g_status' => $request->hardware_status['g_status'] ?: '',
                    'wifi_status' => $request->hardware_status['wifi_status'] ?: '',
                    'bluetooth_status' => $request->hardware_status['bluetooth_status'] ?: '',
                    'hot_water_overage' => $request->overage['hot_water_overage'] ?? 0,
                    'cold_water_overage' => $request->overage['cold_water_overage'] ?? 0,
                    'oxygen_overage' => $request->overage['oxygen_overage'] ?? 0,
                    'air_overage' => $request->overage['air_overage'] ?? 0,
                    'humidity_overage' => $request->overage['humidity_overage'] ?? 0,
                    'filter1_lifespan' => $request->hardware_status['filter1_lifespan'] ?: '',
                    'filter2_lifespan' => $request->hardware_status['filter2_lifespan'] ?: '',
                    'filter3_lifespan' => $request->hardware_status['filter3_lifespan'] ?: '',
                    'temperature' => $request->environment['temperature'] ?? 0,
                    'humidity' => $request->environment['humidity'] ?? 0,
                    'pm2_5' => $request->environment['pm2_5'] ?? 0,
                    'oxygen_concentration' => $request->environment['oxygen_concentration'] ?? 0,
                    'total_produce_water_time' => $request->hardware_status['total_produce_water_time'] ?? 0,
                ]);
                Sterilization::create([
                    'machine_id' => $machine->id,
                    'uv1' => $request->hardware_status['sterilization_time']['uv1'] ?? 0,
                    'uv2' => $request->hardware_status['sterilization_time']['uv2'] ?? 0,
                    'uv3' => $request->hardware_status['sterilization_time']['uv3'] ?? 0,
                    'uv4' => $request->hardware_status['sterilization_time']['uv4'] ?? 0,
                    'uv5' => $request->hardware_status['sterilization_time']['uv5'] ?? 0,
                    'uv6' => $request->hardware_status['sterilization_time']['uv6'] ?? 0,
                ]);
                UserRank::create([
                    'machine_id' => $machine->id,
                    'user_id' => 0,
                    'user_nickname' => 'unknow',
                    'rank' => 0,
                    'machine_rank' => 0
                ]);
            } else {
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
                    'humidity_overage' => $request->overage['humidity_overage'] ?? 0,
                    'filter1_lifespan' => $request->hardware_status['filter1_lifespan'] ?: '',
                    'filter2_lifespan' => $request->hardware_status['filter2_lifespan'] ?: '',
                    'filter3_lifespan' => $request->hardware_status['filter3_lifespan'] ?: '',
                    'temperature' => $request->environment['temperature'] ?? 0,
                    'humidity' => $request->environment['humidity'] ?? 0,
                    'pm2_5' => $request->environment['pm2_5'] ?? 0,
                    'oxygen_concentration' => $request->environment['oxygen_concentration'] ?? 0,
                    'total_produce_water_time' => $request->hardware_status['total_produce_water_time'] ?? 0,
                ]);
                Sterilization::where('machine_id',$machine->id)->update([
                    'machine_id' => $machine->id,
                    'uv1' => $request->hardware_status['sterilization_time']['uv1'] ?? 0,
                    'uv2' => $request->hardware_status['sterilization_time']['uv2'] ?? 0,
                    'uv3' => $request->hardware_status['sterilization_time']['uv3'] ?? 0,
                    'uv4' => $request->hardware_status['sterilization_time']['uv4'] ?? 0,
                    'uv5' => $request->hardware_status['sterilization_time']['uv5'] ?? 0,
                    'uv6' => $request->hardware_status['sterilization_time']['uv6'] ?? 0,
                ]);
            }

            Log::info('Device '.$request->device.' online success!');
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' online error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function setUserRank(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines',
                'user_id' => 'required',
                'user_nickname' => 'required',
                'rank' => 'required',
                'machine_rank' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('device',$request->device)->first();
            UserRank::updateOrCreate(
                ['machine_id' => $machine->id],
                [
                    'user_id' => $request->user_id,
                    'user_nickname' => $request->user_nickname,
                    'rank' => $request->rank,
                    'machine_rank' => $request->machine_rank
                ]
            );
            Log::info('Device '.$request->device.' update user rank success!');
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' update user rank error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function getUserRank(Request $request)
    {
        $machine = Machine::where('device',$request->device)->first();
        return new UserRankResource($machine->userRank);
    }

    public function checkReserve(Request $request)
    {
        return $this->responseSuccessWithMessage('可购买');
    }

    public function checkStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('device',$request->device)->first();
            if (!$machine) {
                return $this->responseErrorWithMessage('非正常状态');
            }

            return $this->responseSuccessWithMessage('在线且数据正常');
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' check status error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function installation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines',
                'hotel_name' => 'required',
                'hotel_code' => 'required',
                'hotel_address' => 'required',
                'room' => 'required',
                'machine_name' => 'required',
                'machine_model' => 'required',
                'installation_date' => 'required',
                'production_date' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('device',$request->device)->first();
            Installation::updateOrCreate(
                ['machine_id' => $machine->id],
                [
                    'hotel_name' => $request->hotel_name,
                    'hotel_code' => $request->hotel_code,
                    'hotel_address' => $request->hotel_address,
                    'room' => $request->room,
                    'machine_name' => $request->machine_name,
                    'machine_model' => $request->machine_model,
                    'installation_date' => $request->installation_date,
                    'production_date' => $request->production_date
                ]
            );
            Log::info('Device '.$request->device.' update installation success!');
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' update installation error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function logfile(Request $request)
    {
        try {
            $base_path = $request['device'] . '/'; //存放目录
            Storage::disk('public')->putFileAs($base_path, $request->file('file'), $request->file('file')->getClientOriginalName());
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Device '.$request['device'].' file upload error: '.$e->getMessage().' Line: '.$e->getLine());
            return $this->responseErrorWithMessage($e->getMessage());
        }
    }
}

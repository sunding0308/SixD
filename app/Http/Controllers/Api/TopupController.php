<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\JPushService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;

class TopupController extends ApiController
{
    //vip码产品信息
    const VIP_CODE_URL = 'https://tminiapps.sixdrops.com/outer/api/msale/sixdropsVipCodeExchange/findVipCodeExchange.do';
    //vip码兑换结果
    const VIP_CODE_RESULT_URL = 'https://tminiapps.sixdrops.com/outer/api/msale/sixdropsVipCodeExchange/findVipCodeExchangeStatus.do';

    public function topup(Request $request, JPushService $jpush)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines',
                'order_number' => 'required',
                'user_id' => 'required',
                'user_nickname' => 'required',
                'product_code' => 'required',
                'product_name' => 'required',
                'purchase_quantity' => 'required',
                'product_unit' => 'required',
                'available_period' => 'required',
                'order_time' => 'required',
                'total_number' => 'required',
                'is_show_red_envelopes' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }
            
            $machine = Machine::where('device',$request->device)->first();
            $water_overage = $machine->water_overage;
            $oxygen_overage = $machine->oxygen_overage;
            $air_overage = $machine->air_overage;
            $humidity_overage = $machine->humidity_overage;
            if ($request->product_name == 'water') {
                $water_overage += $request->purchase_quantity;
            } else if ($request->product_name == 'oxygen') {
                $oxygen_overage += $request->purchase_quantity;
            } else if ($request->product_name == 'air') {
                $air_overage += $request->purchase_quantity;
            } else {
                $humidity_overage += $request->purchase_quantity;
            }

            Machine::where('id',$machine->id)->update([
                'water_overage' => $water_overage,
                'oxygen_overage' => $oxygen_overage,
                'air_overage' => $air_overage,
                'humidity_overage' => $humidity_overage,
            ]);

            //push topup data to machine
            $response = $jpush->push($machine->registration_id, 'topup', $machine->device, [$water_overage,$oxygen_overage,$air_overage,$humidity_overage]);
            if ($response['http_code'] == static::CODE_SUCCESS) {
                Log::info('Device '.$request->device.' topup success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' topup error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function vipTopup(Request $request, JPushService $jpush)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines',
                'vip_code' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('device',$request->device)->first();
            Machine::where('id',$machine->id)->update([
                'water_overage' => 0,
                'oxygen_overage' => 0,
                'air_overage' => 0,
                'humidity_overage' => 0,
            ]);

            $client = new Client();
            //获取VIP码产品信息
            $exchangeResult = $client->request('GET', self::VIP_CODE_URL, [
                'query' => ['machineId' => $machine->device, 'vipCode' => $request->vip_code]
            ]);

            if ($exchangeResult->status !== static::CODE_STATUS_SUCCESS) {
                return $this->responseErrorWithMessage($exchangeResult->msg);
            }
            //兑换结果
            $exchangeStatus = $client->request('GET', self::VIP_CODE_RESULT_URL, [
                'query' => ['machineId' => $request->device, 'vipCode' => $request->vip_code, 'exchangeStatus' => 0]
            ]);

            if ($exchangeStatus->status !== static::CODE_STATUS_SUCCESS) {
                return $this->responseErrorWithMessage($exchangeResult->msg);
            }

            //push topup data to machine
            $response = $jpush->push($machine->registration_id, 'vip_topup', $machine->device, [$exchangeResult]);
            if ($response['http_code'] == static::CODE_SUCCESS) {
                Log::info('Device '.$request->device.' vip topup success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' vip topup error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function resetOverage(Request $request, JPushService $jpush)
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
                'water_overage' => 7200,
                'oxygen_overage' => 0,
                'air_overage' => 0,
                'humidity_overage' => 0,
            ]);

            //push reset data to machine
            $response = $jpush->push($machine->registration_id, 'reset', $machine->device, [7200,0,0,0]);
            if ($response['http_code'] == static::CODE_SUCCESS) {
                Log::info('Device '.$request->device.' reset success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' reset error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

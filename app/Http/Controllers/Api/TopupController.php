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

    private $jpush;
    private $client;

    public function __construct(JPushService $jpush, Client $client)
    {
        $this->jpush = $jpush;
        $this->client = $client;
    }

    public function topup(Request $request)
    {
        try {
            $content = json_decode($request->content);
            $machine = Machine::where('device',$content->device)->first();
            $hot_water_overage = $machine->hot_water_overage;
            $cold_water_overage = $machine->cold_water_overage;
            $oxygen_overage = $machine->oxygen_overage;
            $air_overage = $machine->air_overage;
            $humidity_overage = $machine->humidity_overage;

            Machine::where('id',$machine->id)->update([
                'hot_water_overage' => $hot_water_overage,
                'cold_water_overage' => $cold_water_overage,
                'oxygen_overage' => $oxygen_overage,
                'air_overage' => $air_overage,
                'humidity_overage' => $humidity_overage,
            ]);

            //push topup data to machine
            $response = $this->jpush->push($machine->registration_id, 'topup', $machine->device, [$hot_water_overage,$cold_water_overage,$oxygen_overage,$air_overage,$humidity_overage]);
            if ($response['http_code'] == static::CODE_SUCCESS) {
                Log::info('Device '.$request->device.' topup success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$content->device.' topup error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function getVipProduct(Request $request)
    {
        try {
            //获取VIP码产品信息
            $exchangeResult = $this->client->request('GET', self::VIP_CODE_URL, [
                'query' => ['machineId' => $request->device, 'vipCode' => $request->vip_code]
            ]);
            dd($exchangeResult);

            if ($exchangeStatus->status == static::CODE_STATUS_SUCCESS) {
                return $this->responseSuccessWithMessage($exchangeResult->data);
            } else {
                return $this->responseErrorWithMessage($exchangeResult->msg);
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' get vip product error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function vipTopup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device' => 'required|exists:machines',
                'vip_code' => 'required',
                'exchange_status' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('device',$request->device)->first();
            Machine::where('id',$machine->id)->update([
                'hot_water_overage' => 0,
                'cold_water_overage' => 0,
                'oxygen_overage' => 0,
                'air_overage' => 0,
                'humidity_overage' => 0,
            ]);

            //获取VIP码产品信息
            $exchangeResult = $this->client->request('GET', self::VIP_CODE_URL, [
                'query' => ['machineId' => $machine->device, 'vipCode' => $request->vip_code]
            ]);

            if ($exchangeResult->status !== static::CODE_STATUS_SUCCESS) {
                return $this->responseErrorWithMessage($exchangeResult->msg);
            }
            //兑换结果
            $exchangeStatus = $this->client->request('GET', self::VIP_CODE_RESULT_URL, [
                'query' => ['machineId' => $request->device, 'vipCode' => $request->vip_code, 'exchangeStatus' => 0]
            ]);

            if ($exchangeStatus->status !== static::CODE_STATUS_SUCCESS) {
                return $this->responseErrorWithMessage($exchangeResult->msg);
            }

            //push topup data to machine
            $response = $this->jpush->push($machine->registration_id, 'vip_topup', $machine->device, [$exchangeResult]);
            if ($response['http_code'] == static::CODE_SUCCESS) {
                Log::info('Device '.$request->device.' vip topup success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' vip topup error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }

    public function resetOverage(Request $request)
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
                'hot_water_overage' => 7200,
                'cold_water_overage' => 7200,
                'oxygen_overage' => 0,
                'air_overage' => 0,
                'humidity_overage' => 0,
            ]);

            //push reset data to machine
            $response = $this->jpush->push($machine->registration_id, 'reset', $machine->device, [7200,0,0,0]);
            if ($response['http_code'] == static::CODE_SUCCESS) {
                Log::info('Device '.$request->device.' reset success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' reset error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

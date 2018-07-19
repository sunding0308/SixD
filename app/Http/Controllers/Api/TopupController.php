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
            $response = $this->jpush->push($machine->registration_id, 'topup', $machine->device, [
                $hot_water_overage,
                $cold_water_overage,
                $oxygen_overage,$air_overage,
                $humidity_overage
            ], null, true, $content->is_show_red_envelopes);
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
                'query' => ['machineId' => '11c6f1c9d07c474a9d2da34b1c05681c', 'vipCode' => $request->vip_code]
            ]);
            //处理获取的json
            $exchangeResult = json_decode((string)$exchangeResult->getBody());

            if ($exchangeResult->status == static::CODE_STATUS_SUCCESS) {
                return $this->responseSuccessWithExtrasAndMessage(['data' => $exchangeResult->data], $exchangeResult->msg);
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
                'hot_water_overage' => 'required',
                'cold_water_overage' => 'required',
                'oxygen_overage' => 'required',
                'air_overage' => 'required',
                'humidity_overage' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            //兑换结果
            $exchangeStatus = $this->client->request('GET', self::VIP_CODE_RESULT_URL, [
                'query' => ['machineId' => '11c6f1c9d07c474a9d2da34b1c05681c', 'vipCode' => $request->vip_code, 'exchangeStatus' => $request->exchange_status]
            ]);
            //处理获取的json
            $exchangeStatus = json_decode((string)$exchangeStatus->getBody());

            if ($exchangeStatus->status !== static::CODE_STATUS_SUCCESS) {
                return $this->responseErrorWithMessage($exchangeResult->msg);
            }

            $machine = Machine::where('device',$request->device)->first();
            Machine::where('id',$machine->id)->update([
                'hot_water_overage' => $request->hot_water_overage,
                'cold_water_overage' => $request->cold_water_overage,
                'oxygen_overage' => $request->oxygen_overage,
                'air_overage' => $request->air_overage,
                'humidity_overage' => $request->humidity_overage,
            ]);

            //push topup data to machine
            $response = $this->jpush->push($machine->registration_id, 'topup', $machine->device, [
                $request->hot_water_overage,
                $request->cold_water_overage,
                $request->oxygen_overage,
                $request->air_overage,
                $request->humidity_overage
            ]);
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
            $response = $this->jpush->push($machine->registration_id, 'reset', $machine->device, [7200,7200,0,0,0]);
            if ($response['http_code'] == static::CODE_SUCCESS) {
                Log::info('Device '.$request->device.' reset success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' reset error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

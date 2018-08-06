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
        Log::info($request->content);
        $content = json_decode($request->content);
        $machine = Machine::where('machine_id',$content->machine_id)->first();
        $hot_water_overage = $machine->hot_water_overage;
        $cold_water_overage = $machine->cold_water_overage;
        $oxygen_overage = $machine->oxygen_overage;
        $air_overage = $machine->air_overage;
        $humidity_overage = $machine->humidity_overage;

        $productArr = [
            Machine::CODE_HOT_WATER,
            Machine::CODE_COLD_WATER,
            Machine::CODE_AIR,
            Machine::CODE_OXYGEN,
            Machine::CODE_HUMIDITY
        ];

        foreach($content->product_list as $product) {
            if (in_array($product->product_code, $productArr)) {
                switch ($product->product_code) {
                case Machine::CODE_HOT_WATER:
                    $hot_water_overage += intval(($product->purchase_quantity * 60) / Machine::HOT_WATER_FLOW);
                    break;
                case Machine::CODE_COLD_WATER:
                    $cold_water_overage += intval(($product->purchase_quantity * 60) / Machine::COLD_WATER_FLOW);
                    break;
                case Machine::CODE_AIR:
                    $air_overage += $product->purchase_quantity;
                    break;
                case Machine::CODE_OXYGEN:
                    $oxygen_overage += $product->purchase_quantity;
                    break;
                case Machine::CODE_HUMIDITY:
                    $humidity_overage += $product->purchase_quantity;
                    break;
                default:
                    break;
                }
            }
        }

        //push topup data to machine
        $response = $this->jpush->push($machine->registration_id, 'topup', $machine->device, [
            $hot_water_overage,
            $cold_water_overage,
            $oxygen_overage,
            $air_overage,
            $humidity_overage
        ], null, true, $content->is_show_red_envelopes);
        if ($response && $response['http_code'] == static::CODE_SUCCESS) {
            Log::info('Device '.$machine->device.' topup success!');
            Machine::where('id',$machine->id)->update([
                'hot_water_overage' => $hot_water_overage,
                'cold_water_overage' => $cold_water_overage,
                'oxygen_overage' => $oxygen_overage,
                'air_overage' => $air_overage,
                'humidity_overage' => $humidity_overage
            ]);

            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage('购买失败，请稍后尝试！');
        }
    }

    public function getVipProduct(Request $request)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            //获取VIP码产品信息
            $exchangeResult = $this->client->request('GET', self::VIP_CODE_URL, [
                'query' => ['machineId' => $machine->machine_id, 'vipCode' => $request->vip_code]
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
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines',
            'vip_code' => 'required',
            'exchange_status' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();
        //兑换结果
        $exchangeStatus = $this->client->request('GET', self::VIP_CODE_RESULT_URL, [
            'query' => ['machineId' => $machine->machine_id, 'vipCode' => $request->vip_code, 'exchangeStatus' => $request->exchange_status]
        ]);
        //处理获取的json
        $exchangeStatus = json_decode((string)$exchangeStatus->getBody());

        if ($exchangeStatus->status == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        }

        return $this->responseErrorWithMessage($exchangeResult->msg);
    }

    public function resetOverage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'machine_id' => 'required|exists:machines'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('machine_id',$request->machine_id)->first();

            //push reset data to machine
            $response = $this->jpush->push($machine->registration_id, 'topup', $machine->device, [0,0,0,0,0]);
            if ($response && $response['http_code'] == static::CODE_SUCCESS) {
                Log::info('Device '.$machine->device.' reset success!');
                Machine::where('id',$machine->id)->update([
                    'hot_water_overage' => 0,
                    'cold_water_overage' => 0,
                    'oxygen_overage' => 0,
                    'air_overage' => 0,
                    'humidity_overage' => 0,
                ]);
                
                return $this->responseSuccess();
            } else {
                return $this->responseErrorWithMessage('重置失败，请稍后尝试！');
            }
        } catch (\Exception $e) {
            Log::error('Device '.$machine->device.' reset error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Carbon\Carbon;
use App\PushRecord;
use GuzzleHttp\Client;
use App\Services\IotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;

class TopupController extends ApiController
{
    //vip码产品信息
    const VIP_CODE_URL = 'https://tminiapps.sixdrops.com/outer/api/msale/sixdropsVipCodeExchange/findVipCodeExchange.do';
    //vip码兑换结果
    const VIP_CODE_RESULT_URL = 'https://tminiapps.sixdrops.com/outer/api/msale/sixdropsVipCodeExchange/findVipCodeExchangeStatus.do';

    private $iot;
    private $client;
    
    public function __construct(IotService $iot, Client $client)
    {
        $this->iot = $iot;
        $this->client = $client;
    }

    public function topup(Request $request)
    {
        Log::info($request->content);
        $content = json_decode($request->content);
        $machine = Machine::where('machine_id',$content->machine_id)->first();

        $hot_water_overage = 0;
        $cold_water_overage = 0;
        $air_overage = 0;
        $oxygen_overage = 0;
        $humidity_add_overage = 0;
        $humidity_minus_overage = 0;
        $humidity_child_overage = 0;
        $humidity_adult_overage = 0;

        $productArr = [
            Machine::CODE_HOT_WATER,
            Machine::CODE_COLD_WATER,
            Machine::CODE_AIR,
            Machine::CODE_OXYGEN,
            Machine::CODE_HUMIDIFICATION,
            Machine::CODE_DEHUMIDIFICATION,
            Machine::CODE_CHILD_CONSTANT_HUMIDITY,
            Machine::CODE_ADULT_CONSTANT_HUMIDITY
        ];

        foreach($content->product_list as $product) {
            if (in_array($product->product_code, $productArr)) {
                switch ($product->product_code) {
                case Machine::CODE_HOT_WATER:
                    $hot_water_overage = $product->purchase_quantity;
                    break;
                case Machine::CODE_COLD_WATER:
                    $cold_water_overage = $product->purchase_quantity;
                    break;
                case Machine::CODE_AIR:
                    $air_overage = $product->purchase_quantity;
                    break;
                case Machine::CODE_OXYGEN:
                    $oxygen_overage = $product->purchase_quantity;
                    break;
                case Machine::CODE_HUMIDIFICATION:
                    $humidity_add_overage = $product->purchase_quantity;
                    break;
                case Machine::CODE_DEHUMIDIFICATION:
                    $humidity_minus_overage = $product->purchase_quantity;
                    break;
                case Machine::CODE_CHILD_CONSTANT_HUMIDITY:
                    $humidity_child_overage = $product->purchase_quantity;
                    break;
                case Machine::CODE_ADULT_CONSTANT_HUMIDITY:
                    $humidity_adult_overage = $product->purchase_quantity;
                    break;
                default:
                    break;
                }
            }
        }

        if ($content->is_vip == 'true') {
            $sign = Machine::SIGNAL_VIP_TOPUP;
        } else {
            $sign = Machine::SIGNAL_TOPUP;
        }
        $response = $this->iot->rrpcToWater($sign, $machine->device, [
            $hot_water_overage,
            $cold_water_overage,
            $oxygen_overage,
            $air_overage,
            $humidity_child_overage,
            $humidity_adult_overage,
            $humidity_add_overage,
            $humidity_minus_overage,
        ], null ,true, $content->is_show_red_envelopes);
        if ($response['Success']) {
            Log::info($sign.'--Device: '.$machine->device.' pushed success!');
            if (static::STATUS_SUCCESS == $response['status']) {
                Machine::where('id',$machine->id)->update([
                    'hot_water_overage' => $response['data']['overage'][0],
                    'cold_water_overage' => $response['data']['overage'][1],
                    'oxygen_overage' => $response['data']['overage'][2],
                    'air_overage' => $response['data']['overage'][3],
                    'humidity_child_overage' => $response['data']['overage'][4],
                    'humidity_adult_overage' => $response['data']['overage'][5],
                    'humidity_add_overage' => $response['data']['overage'][6],
                    'humidity_minus_overage' => $response['data']['overage'][7],
                ]);
                Log::info('Device '.$machine->device.' '.$sign.' success!');
    
                return $this->responseSuccess();
            } else {
                Log::error($sign.'--Error: '.$response['message']);
                return $this->responseErrorWithMessage($response['message']);
            }
        } else {
            Log::error($sign.'--Device: '.$machine->device.' pushed fail!');
            return $this->responseErrorWithMessage('推送'.$sign.'到机器失败！');
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
        $exchangeResult = $this->client->request('GET', self::VIP_CODE_RESULT_URL, [
            'query' => ['machineId' => $machine->machine_id, 'vipCode' => $request->vip_code, 'exchangeStatus' => $request->exchange_status]
        ]);
        //处理获取的json
        $exchangeResult = json_decode((string)$exchangeResult->getBody());

        if ($exchangeResult->status == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        }

        return $this->responseErrorWithMessage($exchangeResult->msg);
    }

    public function resetOverage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'machine_id' => 'required|exists:machines',
                'hot_water_overage' => 'required',
                'cold_water_overage' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseErrorWithMessage($validator->errors()->first());
            }

            $machine = Machine::where('machine_id',$request->machine_id)->first();
            $hot_water_overage = $request->hot_water_overage;
            $cold_water_overage = $request->cold_water_overage;
            $response = $this->iot->rrpcToWater(Machine::SIGNAL_RESET, $machine->device, [$hot_water_overage,$cold_water_overage,0,0,0,0,0,0]);
            if ($response['Success']) {
                Log::info($sign.'--Device: '.$machine->device.' pushed success!');
                if (static::STATUS_SUCCESS == $response['status']) {
                    Machine::where('id',$machine->id)->update([
                        'hot_water_overage' => $response['data']['overage'][0],
                        'cold_water_overage' => $response['data']['overage'][1],
                        'oxygen_overage' => $response['data']['overage'][2],
                        'air_overage' => $response['data']['overage'][3],
                        'humidity_child_overage' => $response['data']['overage'][4],
                        'humidity_adult_overage' => $response['data']['overage'][5],
                        'humidity_add_overage' => $response['data']['overage'][6],
                        'humidity_minus_overage' => $response['data']['overage'][7],
                    ]);
                    Log::info('Device '.$machine->device.' reset success!');
    
                    return $this->responseSuccess();
                } else {
                    Log::error($sign.'--Error: '.$response['message']);
                    return $this->responseErrorWithMessage($response['message']);
                }
            } else {
                Log::error($sign.'--Device: '.$machine->device.' pushed fail!');
                return $this->responseErrorWithMessage('推送重置数据到机器失败！');
            }
        } catch (\Exception $e) {
            Log::error('Device '.$machine->device.' reset error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

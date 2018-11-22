<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Carbon\Carbon;
use App\Services\IotService;
use Illuminate\Http\Request;
use App\Services\DubboProxyService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;

class TopupController extends ApiController
{
    //vip码产品信息
    const VIP_CODE_URL = 'com.sixdrops.outer.machinecloud.service.OuterMsaleUserAssembleService';
    //vip码兑换结果
    const VIP_CODE_RESULT_URL = 'com.sixdrops.outer.machinecloud.service.OuterMsaleUserAssembleService';

    private $iot;
    
    public function __construct(IotService $iot)
    {
        $this->iot = $iot;
    }

    public function getVipProduct(Request $request)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            //获取VIP码产品信息
            $service = DubboProxyService::getService(self::VIP_CODE_URL, [
                'registry' => config('dubbo.registry'),
                'version' => config('dubbo.version')
            ]);
            Log::info('获取VIP产品传入参数：('.(string)$machine->machine_id.','.(string)$request->vip_code.')');
            $response = $service->findVipCodeExchange(
                (string)$machine->machine_id,
                (string)$request->vip_code
            );

            if (isset($response['error'])) {
                Log::error('获取VIP产品返回响应：'.$response['error']);
                return $this->responseErrorWithMessage($response['error']);
            }else {
                Log::info('获取VIP产品返回响应：成功');
                return $this->responseSuccessWithExtrasAndMessage(['data' => $response]);
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
        $service = DubboProxyService::getService(self::VIP_CODE_RESULT_URL, [
            'registry' => config('dubbo.registry'),
            'version' => config('dubbo.version')
        ]);
        Log::info('兑换VIP产品传入参数：('.(string)$machine->machine_id.','.(string)$request->vip_code.','.(string)$request->exchange_status.')');
        $response = $service->executeFindVipCodeExchangeStatus(
            (string)$machine->machine_id,
            (string)$request->vip_code,
            (string)$request->exchange_status
        );

        if ($response == 0) {
            Log::info('兑换VIP产品返回响应：成功');
            return $this->responseSuccess();
        } else if ($response == 1) {
            Log::error('兑换VIP产品返回响应：该VIP码已被使用过');
            return $this->responseErrorWithMessage('该VIP码已被使用过');
        } else if ($response == 2) {
            Log::error('兑换VIP产品返回响应：该VIP码已过期失效');
            return $this->responseErrorWithMessage('该VIP码已过期失效');
        } else if ($response == 3) {
            Log::error('兑换VIP产品返回响应：该VIP码无效');
            return $this->responseErrorWithMessage('该VIP码无效');
        } else if ($response == 4) {
            Log::error('兑换VIP产品返回响应：该VIP不能在该酒店使用');
            return $this->responseErrorWithMessage('该VIP不能在该酒店使用');
        } else if ($response == 5) {
            Log::error('兑换VIP产品返回响应：机器不存在');
            return $this->responseErrorWithMessage('机器不存在');
        } else {
            Log::error('兑换VIP产品返回响应：兑换失败');
            return $this->responseErrorWithMessage('兑换失败');
        }
    }
}

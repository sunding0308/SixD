<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Carbon\Carbon;
use App\PushRecord;
use App\Services\IotService;
use Illuminate\Http\Request;
use App\Services\DubboProxyService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;

class PushController extends ApiController
{
    //紧急服务申请
    const URGENT_SERVICE_URL = 'com.sixdrops.outer.machinecloud.service.OuterMachineMaintenanceApplyService';
    //紧急服务完成
    const URGENT_SERVICE_COMPLETE_URL = 'com.sixdrops.outer.machinecloud.service.OuterMachineMaintenanceApplyService';
    //普通服务内容
    const ORDINARY_SERVICE_URL = 'com.sixdrops.outer.machinecloud.service.OuterMachineMaintenanceApplyService';
    //单项服务完成
    const SINGLE_ORDINARY_SERVICE_COMPLETE_URL = 'com.sixdrops.outer.machinecloud.service.OuterMachineMaintenanceApplyService';
    //普通服务完成
    const ALL_ORDINARY_SERVICE_COMPLETE_URL = 'com.sixdrops.outer.machinecloud.service.OuterMachineMaintenanceApplyService';
    //维护申请
    const MAINTENANCE_URL = 'com.sixdrops.outer.machinecloud.service.OuterMachineMaintenanceApplyService';
    //维护完成
    const MAINTENANCE_COMPLETE_URL = 'com.sixdrops.outer.machinecloud.service.OuterMachineMaintenanceApplyService';
    //获取红包二维码
    const RED_PACKAGE_QR_CODE_URL = 'com.sixdrops.outer.machinecloud.service.OuterMsaleUserAssembleService';
    //更换备用箱
    const REPLACE_CONTAINER_URL = 'com.sixdrops.outer.machinecloud.service.OuterMachineMaintenanceApplyService';
    //补仓
    const REPLENISHMENT_URL = 'com.sixdrops.outer.machinecloud.service.OuterMachineOssService';

    private $iot;
    
    public function __construct(IotService $iot)
    {
        $this->iot = $iot;
    }

    /**
     * push signal to machine
     */
    public function pushOverageSignal(Request $request)
    {
        return $this->pushSignal(Machine::SIGNAL_OVERAGE, $request->device);
    }

    public function pushHardwareStatusSignal(Request $request)
    {
        return $this->pushSignal(Machine::SIGNAL_HARDWARE_STATUS, $request->device);
    }

    public function pushRecordsSignal(Request $request)
    {
        return $this->pushSignal(Machine::SIGNAL_RECORDS, $request->device);
    }

    public function pushEnvironmentSignal(Request $request)
    {
        return $this->pushSignal(Machine::SIGNAL_ENVIROMENT, $request->device);
    }

    public function pushWaterQualityStatisticsSignal(Request $request)
    {
        return $this->pushSignal(Machine::SIGNAL_WATER_QUALITY_STATISTICS, $request->device);
    }

    public function pushAppMenuAnalysisSignal(Request $request)
    {
        return $this->iot->rrpcToWater(Machine::SIGNAL_APP_MENU_ANALYSIS, $request->device);
    }

    public function pushApiAnalysisSignal(Request $request)
    {
        return $this->iot->rrpcToWater(Machine::SIGNAL_API_ANALYSIS, $request->device);
    }

    private function pushSignal($sign, $device)
    {
        if (!$device) {
            //push signal to machines every hour
            $this->multiplePush($sign);
        } else {
            //push to machine by manual control
            return $this->singlePush($sign, $device);
        }
    }

    private function multiplePush($sign)
    {
        $devices = Machine::pluck('device');
        foreach($devices as $device) {
            $machine = Machine::where('devices', $devices)->first();
            $response = $this->iot->rrpcToWater($sign, $machine->device);
            if ($response['Success']) {
                if (static::STATUS_SUCCESS == $response['status']) {
                    Log::info($sign.'--Device: '.$machine->device.' pushed success!');
                } else {
                    Log::error($sign.'--Error: '.$response['message']);
                }
            } else {
                Log::error($sign.'--Device: '.$machine->device.' pushed fail!');
            }
        }
    }

    private function singlePush($sign, $device)
    {
        $machine = Machine::where('device', $device)->first();
        $response = $this->iot->rrpcToWater($sign, $machine->device);
        if ($response['Success']) {
            if (static::STATUS_SUCCESS == $response['status']) {
                Log::info($sign.'--Device: '.$machine->device.' pushed success!');
                return response()->json([
                    'http_code' => static::CODE_SUCCESS
                ]);
            } else {
                Log::error($sign.'--Error: '.$response['message']);
            }
        } else {
            Log::error($sign.'--Device: '.$machine->device.' pushed fail!');
            return response()->json([
                'http_code' => static::CODE_ERROR,
                'msg' => '设备未在线，获取信息失败！'
            ]);
        }
    }

    /**
     * push to data cloud
     */

    /**
     * SJY018
     * SJY021
     * 紧急服务
     */
    public function pushUrgentServiceToDataCloud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines',
            'service_content' => 'required',
            'maintenance_status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::with('installation')->where('device',$request->device)->first();
        if ($request->maintenance_status == static::CODE_STATUS_NOT_COMPLETE) {
            //紧急服务申请
            $service = DubboProxyService::getService(self::URGENT_SERVICE_URL, [
                'registry' => config('dubbo.registry'),
                'version' => config('dubbo.version')
            ]);
            Log::info(
                '紧急服务申请传入参数：('.
                (string)$machine->machine_id.','.
                (string)$machine->installation->hotel_name.','.
                (string)$machine->installation->hotel_code.','.
                (string)$machine->installation->hotel_address.','.
                (string)$machine->installation->room.','.
                (string)$request->service_content.')'
            );
            $response = $service->executeModifyUrgentServiceTicketApply(
                (string)$machine->machine_id,
                (string)$machine->installation->hotel_name,
                (string)$machine->installation->hotel_code,
                (string)$machine->installation->hotel_address,
                (string)$machine->installation->room,
                (string)$request->service_content
            );
        } else {
            //紧急服务完成
            $service = DubboProxyService::getService(self::URGENT_SERVICE_COMPLETE_URL, [
                'registry' => config('dubbo.registry'),
                'version' => config('dubbo.version')
            ]);
            Log::info(
                '紧急服务完成传入参数：('.
                (string)$machine->machine_id.','.
                (string)$request->service_content.','.
                (string)$request->maintenance_status.')'
            );
            $response = $service->executeSaveUrgentServiceTicketComplete(
                (string)$machine->machine_id,
                (string)$request->service_content,
                (string)$request->maintenance_status
            );
        }

        if ($response == static::CODE_STATUS_SUCCESS) {
            Log::info('紧急服务返回响应：成功');
        } else if ($response == static::CODE_STATUS_MACHINE_NOT_EXIST) {
            Log::error('紧急服务返回响应：机器不存在');
        } else {
            Log::error('紧急服务返回响应：失败');
        }

        return $this->responseSuccess();
    }

    /**
     * SJY018
     * SJY021
     * 普通服务内容
     */
    public function pushOrdinaryServiceToDataCloud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines',
            'service_content' => 'required',
            'maintenance_status' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();        
        if ($request->maintenance_status == static::CODE_STATUS_NOT_COMPLETE) {
            //普通服务申请
            $service = DubboProxyService::getService(self::ORDINARY_SERVICE_URL, [
                'registry' => config('dubbo.registry'),
                'version' => config('dubbo.version')
            ]);
            Log::info(
                '普通服务申请传入参数：('.
                (string)$machine->machine_id.','.
                (string)$request->service_content.')'
            );
            $response = $service->executeModifyOuterMachineMaintenanceApply(
                (string)$machine->machine_id,
                (string)$request->service_content
            );
        } else {
            //普通服务完成
            $service = DubboProxyService::getService(self::ALL_ORDINARY_SERVICE_COMPLETE_URL, [
                'registry' => config('dubbo.registry'),
                'version' => config('dubbo.version')
            ]);
            Log::info(
                '普通服务完成传入参数：('.
                (string)$machine->machine_id.','.
                (string)$request->service_content.','.
                (string)$request->maintenance_status.')'
            );
            $response = $service->executeSaveOrdinaryServiceTicketComplete(
                (string)$machine->machine_id,
                (string)$request->service_content,
                (string)$request->maintenance_status
            );
        }

        if ($response == static::CODE_STATUS_SUCCESS) {
            Log::info('普通服务返回响应：成功');
        } else if ($response == static::CODE_STATUS_MACHINE_NOT_EXIST) {
            Log::error('普通服务返回响应：机器不存在');
        } else {
            Log::error('普通服务返回响应：失败');
        }

        return $this->responseSuccess();
    }

    /**
     * SJY020
     * 单项服务完成
     */
    public function pushSingleOrdinaryServiceCompleteToDataCloud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines',
            'step_name' => 'required',
            'process_status' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first(); 
        $service = DubboProxyService::getService(self::SINGLE_ORDINARY_SERVICE_COMPLETE_URL, [
            'registry' => config('dubbo.registry'),
            'version' => config('dubbo.version')
        ]);
        Log::info(
            '单项服务完成传入参数：('.
            (string)$machine->machine_id.','.
            (string)$request->step_name.','.
            (string)$request->process_status.')'
        );
        $response = $service->executeModifyOuterMachineMaintenanceStep(
            (string)$machine->machine_id,
            (string)$request->step_name,
            (string)$request->process_status
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            Log::info('单项服务完成返回响应：成功');
        } else if ($response == static::CODE_STATUS_MACHINE_NOT_EXIST) {
            Log::error('单项服务完成返回响应：机器不存在');
        } else {
            Log::error('单项服务完成返回响应：失败');
        }

        return $this->responseSuccess();
    }

    /**
     * SJY025
     * SJY029
     * 维护
     */
    public function pushMaintenanceToDataCloud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines',
            'service_content' => 'required',
            'maintenance_status' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first(); 
        if ($request->maintenance_status == static::CODE_STATUS_NOT_COMPLETE) {
            //维护申请
            $service = DubboProxyService::getService(self::MAINTENANCE_URL, [
                'registry' => config('dubbo.registry'),
                'version' => config('dubbo.version')
            ]);
            Log::info(
                '维护申请传入参数：('.
                (string)$machine->machine_id.','.
                (string)$request->service_content.')'
            );
            $response = $service->executeModifyMaintenanceTicketApply(
                (string)$machine->machine_id,
                (string)$request->service_content
            );
        } else {
            //维护完成
            $service = DubboProxyService::getService(self::MAINTENANCE_COMPLETE_URL, [
                'registry' => config('dubbo.registry'),
                'version' => config('dubbo.version')
            ]);
            Log::info(
                '维护完成传入参数：('.
                (string)$machine->machine_id.','.
                (string)$request->service_content.','.
                (string)$request->maintenance_status.')'
            );
            $response = $service->executeSaveMaintenanceTicketComplete(
                (string)$machine->machine_id,
                (string)$request->service_content,
                (string)$request->maintenance_status
            );
        }

        if ($response == static::CODE_STATUS_SUCCESS) {
            Log::info('维护返回响应：成功');
        } else if ($response == static::CODE_STATUS_MACHINE_NOT_EXIST) {
            Log::error('维护返回响应：机器不存在');
        } else {
            Log::error('维护返回响应：失败');
        }

        return $this->responseSuccess();
    }

    /**
     * HB001
     * 获取红包二维码
     */
    public function pushUseStatusToDataCloud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();
        $service = DubboProxyService::getService(self::RED_PACKAGE_QR_CODE_URL, [
            'registry' => config('dubbo.registry'),
            'version' => config('dubbo.version')
        ]);
        Log::info('获取红包二维码传入参数：('.(string)$machine->machine_id.','.(string)'1'.')');
        $response = $service->findRedPackageQRcode((string)$machine->machine_id,(string)'1');

        if ($response == static::CODE_STATUS_SUCCESS) {
            Log::info('获取红包二维码返回响应：成功');
        } else if ($response == static::CODE_STATUS_MACHINE_NOT_EXIST) {
            Log::error('获取红包二维码返回响应：机器不存在');
        } else {
            Log::error('获取红包二维码返回响应：失败');
        }

        return $this->responseSuccess();
    }

    /**
     * WS013
     * 更换备用箱申请
     */
    public function pushReplaceContainerToDataCloud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines',
            'position' => 'required',
            'service_content' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();
        $service = DubboProxyService::getService(self::REPLACE_CONTAINER_URL, [
            'registry' => config('dubbo.registry'),
            'version' => config('dubbo.version')
        ]);
        Log::info(
            '更换备用箱申请传入参数：('.
            (string)$machine->machine_id.','.
            $request->position.','.
            (string)$request->service_content.')'
        );
        $response = $service->executeApplyReplaceContainerService(
            (string)$machine->machine_id,
            $request->position,
            (string)$request->service_content
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            Log::info('更换备用箱申请返回响应：成功');
        } else {
            Log::error('更换备用箱申请返回响应：失败');
        }

        return $this->responseSuccess();
    }

    /**
     * WS017
     * 更换备用箱完成
     */
    public function pushReplaceContainerCompleteToDataCloud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines',
            'complete_status' => 'required',
            'position_up' => 'required',
            'serial_up' => 'required',
            'position_down' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();
        $service = DubboProxyService::getService(self::REPLACE_CONTAINER_URL, [
            'registry' => config('dubbo.registry'),
            'version' => config('dubbo.version')
        ]);
        Log::info(
            '更换备用箱完成传入参数：('.
            (string)$machine->machine_id.','.
            (string)$request->complete_status.','.
            $request->position_up.','.
            (string)$request->serial_up.','.
            $request->position_down.','.
            (string)$request->serial_down.')'
        );
        $response = $service->executeCompleteReplaceContainerService(
            (string)$machine->machine_id,
            (string)$request->complete_status,
            $request->position_up,
            (string)$request->serial_up,
            $request->position_down,
            (string)$request->serial_down
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            Log::info('更换备用箱完成返回响应：成功');
        } else {
            Log::error('更换备用箱完成返回响应：失败');
        }

        return $this->responseSuccess();
    }

    /**
     * WS006
     * 发送补仓申请
     */
    public function pushReplenishmentToDataCloud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines',
            'position' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();
        $service = DubboProxyService::getService(self::REPLENISHMENT_URL, [
            'registry' => config('dubbo.registry'),
            'version' => config('dubbo.version')
        ]);
        Log::info(
            '发送补仓申请传入参数：('.
            (string)$machine->machine_id.','.
            $request->position.')'
        );
        $response = $service->executeSaveOuterMachineOssApply(
            (string)$machine->machine_id,
            $request->position
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            Log::info('发送补仓申请返回响应：成功');
        } else {
            Log::error('发送补仓申请返回响应：失败');
        }

        return $this->responseSuccess();
    }

    /**
     * WS010
     * 检测补货完成
     */
    public function pushReplenishmentCompleteToDataCloud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines',
            'position' => 'required',
            'serial' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();
        $service = DubboProxyService::getService(self::REPLENISHMENT_URL, [
            'registry' => config('dubbo.registry'),
            'version' => config('dubbo.version')
        ]);
        Log::info(
            '补货完成传入参数：('.
            (string)$machine->machine_id.','.
            $request->position.','.
            (string)$request->serial.')'
        );
        $response = $service->executeSaveOuterMachineOssComplete(
            (string)$machine->machine_id,
            $request->position,
            (string)$request->serial
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            Log::info('补货完成返回响应：成功');
        } else {
            Log::error('补货完成返回响应：失败');
        }

        return $this->responseSuccess();
    }

    /**
     * WS019
     * 微售卖机加库存
     */
    public function pushVendingAddStockToDataCloud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|exists:machines',
            'position' => 'required',
            'container_id' => 'required',
            'container_position' => 'required',
            'serial' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();
        $service = DubboProxyService::getService(self::REPLENISHMENT_URL, [
            'registry' => config('dubbo.registry'),
            'version' => config('dubbo.version')
        ]);
        Log::info(
            '微售卖机加库存传入参数：('.
            (string)$machine->machine_id.','.
            $request->position.','.
            (string)$request->container_id.','.
            $request->container_position.','.
            (string)$request->serial.')'
        );
        $response = $service->executeSaveMachinePosition(
            (string)$machine->machine_id,
            $request->position,
            (string)$request->container_id,
            $request->container_position,
            (string)$request->serial
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            Log::info('微售卖机加库存返回响应：成功');
        } else {
            Log::error('微售卖机加库存返回响应：失败');
        }

        return $this->responseSuccess();
    }
}

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

    public function pushRedpacketQrCodeSignal(Request $request)
    {
        $machine = Machine::where('machine_id', $request->machine_id)->first();
        $response = $this->iot->rrpcToWater(Machine::SIGNAL_REDPACKET, $machine->device, [], null, true, false, $request->redpacket_qr_code);
        if ($response['Success']) {
            if (static::STATUS_SUCCESS == $response['status']) {
                Log::info(Machine::SIGNAL_REDPACKET.'--Device: '.$machine->device.' pushed success!');
                return $this->responseSuccess();
            } else {
                Log::error(Machine::SIGNAL_REDPACKET.'--Error: '.$response['message']);
                return $this->responseErrorWithMessage($response['message']);
            }
        } else {
            Log::error(Machine::SIGNAL_REDPACKET.'--Device: '.$machine->device.' pushed fail!');
            return $this->responseErrorWithMessage('推送红包二维码到机器失败！');
        }
    }

    public function pushRedpacketReceivedSignal(Request $request)
    {
        $machine = Machine::where('machine_id', $request->machine_id)->first();
        return $this->pushSignal(Machine::SIGNAL_REDPACKET_RECEIVED, $machine->device);
    }

    public function pushInstallationCompletedSignal(Request $request)
    {
        $machine = Machine::where('machine_id', $request->machine_id)->first();
        $response = $this->iot->rrpcToWater(Machine::SIGNAL_INSTALLATION, $machine->device, [], $request->account_type);
        if ($response['Success']) {
            if (static::STATUS_SUCCESS == $response['status']) {
                Log::info(Machine::SIGNAL_INSTALLATION.'--Device: '.$machine->device.' pushed success!');
                return $this->responseSuccess();
            } else {
                Log::error(Machine::SIGNAL_INSTALLATION.'--Error: '.$response['message']);
                return $this->responseErrorWithMessage($response['message']);
            }
        } else {
            Log::error(Machine::SIGNAL_INSTALLATION.'--Device: '.$machine->device.' pushed fail!');
            return $this->responseErrorWithMessage('推送安装人员类型到机器失败！');
        }
    }

    public function pushAppMenuAnalysisSignal(Request $request)
    {
        return $this->iot->rrpcToWater(Machine::SIGNAL_APP_MENU_ANALYSIS, $request->device);
    }

    public function pushApiAnalysisSignal(Request $request)
    {
        return $this->iot->rrpcToWater(Machine::SIGNAL_API_ANALYSIS, $request->device);
    }

    public function pushUrgentAccountType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'machine_id' => 'required|exists:machines',
            'account_type' => 'required',
            'is_same_person' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('machine_id',$request->machine_id)->first();
        $response = $this->iot->rrpcToWater(Machine::SIGNAL_ACCOUT_TYPE, $machine->device, [], $request->account_type, $request->is_same_person);
        if ($response['Success']) {
            if (static::STATUS_SUCCESS == $response['status']) {
                Log::info(Machine::SIGNAL_ACCOUT_TYPE.'--Device: '.$machine->device.' pushed success!');
                return $this->responseSuccess();
            } else {
                Log::error(Machine::SIGNAL_ACCOUT_TYPE.'--Error: '.$response['message']);
                return $this->responseErrorWithMessage($response['message']);
            }
        } else {
            Log::error(Machine::SIGNAL_ACCOUT_TYPE.'--Device: '.$machine->device.' pushed fail!');
            return $this->responseErrorWithMessage('推送紧急账户类型到机器失败！');
        }
    }

    public function pushAccountType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'machine_id' => 'required|exists:machines',
            'account_type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('machine_id',$request->machine_id)->first();
        $response = $this->iot->rrpcToWater(Machine::SIGNAL_ACCOUT_TYPE, $machine->device, [], $request->account_type);
        if ($response['Success']) {
            if (static::STATUS_SUCCESS == $response['status']) {
                Log::info(Machine::SIGNAL_ACCOUT_TYPE.'--Device: '.$machine->device.' pushed success!');
                return $this->responseSuccess();
            } else {
                Log::error(Machine::SIGNAL_ACCOUT_TYPE.'--Error: '.$response['message']);
                return $this->responseErrorWithMessage($response['message']);
            }
        } else {
            Log::error(Machine::SIGNAL_ACCOUT_TYPE.'--Device: '.$machine->device.' pushed fail!');
            return $this->responseErrorWithMessage('推送账户类型到机器失败！');
        }
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

    //紧急服务
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
            $response = $service->modifyUrgentServiceTicketApply(
                $machine->machine_id,
                $machine->installation->hotel_name,
                $machine->installation->hotel_code,
                $machine->installation->hotel_address,
                $machine->installation->room,
                $request->service_content
            );
        } else {
            //紧急服务完成
            $service = DubboProxyService::getService(self::URGENT_SERVICE_COMPLETE_URL, [
                'registry' => config('dubbo.registry'),
                'version' => config('dubbo.version')
            ]);
            $response = $service->saveUrgentServiceTicketComplete(
                $machine->machine_id,
                $request->service_content,
                $request->maintenance_status
            );
        }

        if ($response == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else if ($response == static::CODE_STATUS_MACHINE_NOT_EXIST) {
            return $this->responseErrorWithMessage('机器不存在');
        } else {
            return $this->responseErrorWithMessage();
        }
    }

    //普通服务内容
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
            $response = $service->modifyOuterMachineMaintenanceApply(
                $machine->machine_id,
                $request->service_content
            );
        } else {
            //普通服务完成
            $service = DubboProxyService::getService(self::ALL_ORDINARY_SERVICE_COMPLETE_URL, [
                'registry' => config('dubbo.registry'),
                'version' => config('dubbo.version')
            ]);
            $response = $service->saveOrdinaryServiceTicketComplete(
                $machine->machine_id,
                $request->service_content,
                "$request->maintenance_status"
            );
        }

        if ($response == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else if ($response == static::CODE_STATUS_MACHINE_NOT_EXIST) {
            return $this->responseErrorWithMessage('机器不存在');
        } else {
            return $this->responseErrorWithMessage();
        }
    }

    //单项服务完成
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
        $response = $service->modifyOuterMachineMaintenanceStep(
            $machine->machine_id,
            $request->step_name,
            "$request->process_status"
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else if ($response == static::CODE_STATUS_MACHINE_NOT_EXIST) {
            return $this->responseErrorWithMessage('机器不存在');
        } else {
            return $this->responseErrorWithMessage();
        }
    }

    //维护
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
            $response = $service->modifyMaintenanceTicketApply(
                $machine->machine_id,
                $request->service_content
            );
        } else {
            //维护完成
            $service = DubboProxyService::getService(self::MAINTENANCE_COMPLETE_URL, [
                'registry' => config('dubbo.registry'),
                'version' => config('dubbo.version')
            ]);
            $response = $service->saveMaintenanceTicketComplete(
                $machine->machine_id,
                $request->service_content,
                "$request->maintenance_status"
            );
        }

        if ($response == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else if ($response == static::CODE_STATUS_MACHINE_NOT_EXIST) {
            return $this->responseErrorWithMessage('机器不存在');
        } else {
            return $this->responseErrorWithMessage();
        }
    }

    //水机使用状态
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
        $response = $service->findRedPackageQRcode($machine->machine_id,"1");

        if ($response == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else if ($response == static::CODE_STATUS_MACHINE_NOT_EXIST) {
            return $this->responseErrorWithMessage('机器不存在');
        } else {
            return $this->responseErrorWithMessage();
        }
    }

    //更换备用箱申请
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
        $response = $service->applyReplaceContainerService(
            $machine->machine_id,
            $request->position,
            $request->service_content
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage();
        }
    }

    //更换备用箱完成
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
        $response = $service->completeReplaceContainerService(
            $machine->machine_id,
            $request->complete_status,
            $request->position_up,
            $request->serial_up,
            $request->position_down,
            $request->serial_down
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage();
        }
    }

    //发送补仓申请
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
        $response = $service->saveOuterMachineOssApply(
            $machine->machine_id,
            $request->position
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage();
        }
    }

    //检测补货完成
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
        $response = $service->saveOuterMachineOssComplete(
            $machine->machine_id,
            $request->position,
            $request->serial
        );

        if ($response == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage();
        }
    }

        //微售卖机加库存
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
            $response = $service->saveMachinePosition(
                $machine->machine_id,
                $request->position,
                $request->container_id,
                $request->container_position,
                $request->serial
            );
    
            if ($response == static::CODE_STATUS_SUCCESS) {
                return $this->responseSuccess();
            } else {
                return $this->responseErrorWithMessage();
            }
        }
}

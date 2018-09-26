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

class PushController extends ApiController
{
    const BASE_URL = 'https://tminiapps.sixdrops.com/outer';
    //紧急服务申请
    const URGENT_SERVICE_URL = self::BASE_URL . '/api/machine/urgentServiceTicket/urgentServiceTicketApply.do';
    //紧急服务完成
    const URGENT_SERVICE_COMPLETE_URL = self::BASE_URL . '/api/machine/urgentServiceTicket/urgentServiceTicketComplete.do';
    //普通服务内容
    const ORDINARY_SERVICE_URL = self::BASE_URL . '/api/machine/ordinaryServiceTicket/pushOrdinaryServiceContent.do';
    //单项服务完成
    const SINGLE_ORDINARY_SERVICE_COMPLETE_URL = self::BASE_URL . '/api/machine/ordinaryServiceTicket/getOrdinaryServiceTicket.do';
    //普通服务完成
    const ALL_ORDINARY_SERVICE_COMPLETE_URL = self::BASE_URL . '/api/machine/ordinaryServiceTicket/getOrdinaryServiceTicketComplete.do';
    //维护申请
    const MAINTENANCE__URL = self::BASE_URL . '/api/machine/maintenanceTicket/maintenanceTicketApply.do';
    //维护完成
    const MAINTENANCE_COMPLETE_URL = self::BASE_URL . '/api/machine/maintenanceTicket/getMaintenanceTicketComplete.do';
    //水机使用状态
    const MACHINE_USE_STATUS_URL = self::BASE_URL . '/api/msale/sixdropsVipCodeExchange/findRedPackageQRcode.do';

    private $iot;
    private $client;
    
    public function __construct(IotService $iot, Client $client)
    {
        $this->iot = $iot;
        $this->client = $client;
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
        $response = $this->iot->rrpc(Machine::SIGNAL_REDPACKET, $machine->device, [], null, true, false, $request->redpacket_qr_code);
        if ($response['Success']) {
            Log::info(Machine::SIGNAL_REDPACKET.'--Device: '.$machine->device.' pushed success!');
            return $this->responseSuccess();
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

    public function pushAppMenuAnalysisSignal(Request $request)
    {
        return $this->iot->rrpc(Machine::SIGNAL_APP_MENU_ANALYSIS, $request->device);
    }

    public function pushApiAnalysisSignal(Request $request)
    {
        return $this->iot->rrpc(Machine::SIGNAL_API_ANALYSIS, $request->device);
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
        $response = $this->iot->rrpc(Machine::SIGNAL_ACCOUT_TYPE, $machine->device, [], $request->account_type, $request->is_same_person);
        if ($response['Success']) {
            Log::info(Machine::SIGNAL_ACCOUT_TYPE.'--Device: '.$machine->device.' pushed success!');
            return $this->responseSuccess();
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
        $response = $this->iot->rrpc(Machine::SIGNAL_ACCOUT_TYPE, $machine->device, [], $request->account_type);
        if ($response['Success']) {
            Log::info(Machine::SIGNAL_ACCOUT_TYPE.'--Device: '.$machine->device.' pushed success!');
            return $this->responseSuccess();
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
            $response = $this->iot->rrpc($sign, $machine->device);
            if ($response['Success']) {
                Log::info($sign.'--Device: '.$machine->device.' pushed success!');
            } else {
                Log::error($sign.'--Device: '.$machine->device.' pushed fail!');
            }
        }
    }

    private function singlePush($sign, $device)
    {
        $machine = Machine::where('device', $device)->first();
        $response = $this->iot->rrpc($sign, $machine->device);
        if ($response['Success']) {
            Log::info($sign.'--Device: '.$machine->device.' pushed success!');
            return response()->json([
                'http_code' => static::CODE_SUCCESS
            ]);
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
            $response = $this->client->request('POST', self::URGENT_SERVICE_URL, [
                'form_params' => [
                    'machineId' => $machine->machine_id,
                    'hotelName' => $machine->installation->hotel_name,
                    'hotelId' => $machine->installation->hotel_code,
                    'hotelAddress' => $machine->installation->hotel_address,
                    'roomNo' => $machine->installation->room,
                    'serviceContent' => $request->service_content
                ]
            ]);
        } else {
            //紧急服务完成
            $response = $this->client->request('POST', self::URGENT_SERVICE_COMPLETE_URL, [
                'form_params' => [
                    'machineId' => $machine->machine_id,
                    'serviceContent' => $request->service_content,
                    'maintenanceStatus' => $request->maintenance_status
                ]
            ]);
        }
        //处理获取的json
        $response = json_decode((string)$response->getBody());

        if ($response->status == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage($response->msg);
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
            $response = $this->client->request('POST', self::ORDINARY_SERVICE_URL, [
                'form_params' => [
                    'machineId' => $machine->machine_id,
                    'serviceContent' => $request->service_content
                ]
            ]);
        } else {
            //普通服务完成
            $response = $this->client->request('POST', self::ALL_ORDINARY_SERVICE_COMPLETE_URL, [
                'form_params' => [
                    'machineId' => $machine->machine_id,
                    'serviceContent' => $request->service_content,
                    'maintenanceStatus' => static::CODE_STATUS_COMPLETE
                ]
            ]);
        }
        //处理获取的json
        $response = json_decode((string)$response->getBody());

        if ($response->status == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage($response->msg);
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
        $response = $this->client->request('POST', self::SINGLE_ORDINARY_SERVICE_COMPLETE_URL, [
            'form_params' => [
                'machineId' => $machine->machine_id,
                'stepName' => $request->step_name,
                'processStatus' => $request->process_status
            ]
        ]);
        //处理获取的json
        $response = json_decode((string)$response->getBody());

        if ($response->status == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage($response->msg);
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
            $response = $this->client->request('POST', self::MAINTENANCE__URL, [
                'form_params' => [
                    'machineId' => $machine->machine_id,
                    'serviceContent' => $request->service_content
                ]
            ]);
        } else {
            //维护完成
            $response = $this->client->request('POST', self::MAINTENANCE_COMPLETE_URL, [
                'form_params' => [
                    'machineId' => $machine->machine_id,
                    'serviceContent' => $request->service_content,
                    'maintenanceStatus' => $request->maintenance_status
                ]
            ]);
        }
        //处理获取的json
        $response = json_decode((string)$response->getBody());

        if ($response->status == static::CODE_STATUS_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage($response->msg);
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
            $response = $this->client->request('POST', self::MACHINE_USE_STATUS_URL, [
                'form_params' => [
                    'machineId' => $machine->machine_id,
                    'useStatus' => 1
                ]
            ]);
            //处理获取的json
            $response = json_decode((string)$response->getBody());
    
            if ($response->status == static::CODE_STATUS_SUCCESS) {
                return $this->responseSuccess();
            } else {
                return $this->responseErrorWithMessage($response->msg);
            }
        }
}

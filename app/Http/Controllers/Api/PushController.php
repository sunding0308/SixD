<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Carbon\Carbon;
use App\PushRecord;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\JPushService;
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

    private $jpush;
    private $client;
    
    public function __construct(JPushService $jpush, Client $client)
    {
        $this->jpush = $jpush;
        $this->client = $client;
    }

    /**
     * push signal to machine
     */
    public function pushOverageSignal(Request $request)
    {
        return $this->pushSignal($request->registrationId, 'overage');
    }

    public function pushHardwareStatusSignal(Request $request)
    {
        return $this->pushSignal($request->registrationId, 'hardware_status');
    }

    public function pushRecordsSignal(Request $request)
    {
        return $this->pushSignal($request->registrationId, 'records');
    }

    public function pushEnvironmentSignal(Request $request)
    {
        return $this->pushSignal($request->registrationId, 'environment');
    }

    public function pushWaterQualityStatisticsSignal(Request $request)
    {
        return $this->pushSignal($request->registrationId, 'water_quality_statistics');
    }

    public function pushRedpacketQrCodeSignal(Request $request)
    {
        $machine = Machine::where('machine_id', $request->machine_id)->first();
        $pushed_at = Carbon::now()->timestamp;
        $response = $this->jpush->push($machine->registration_id, 'redpacket', $pushed_at, $request->redpacket_qr_code);
        if ($response['http_code'] == static::CODE_SUCCESS) {
            Log::info('Machine id: '.$machine->id.' pushed success!');
            PushRecord::create([
                'machine_id' => $machine->id,
                'type' => 'redpacket',
                'pushed_at' => $pushed_at,
            ]);
            return response()->json([
                'http_code' => static::CODE_SUCCESS
            ]);
        } else {
            Log::error('Machine id: '.$machine->id.' pushed fail!');
            return response()->json([
                'http_code' => static::CODE_ERROR,
                'msg' => '网络糟糕，获取信息失败！'
            ]);
        }
    }

    public function pushRedpacketReceivedSignal(Request $request)
    {
        $machine = Machine::where('machine_id', $request->machine_id)->first();
        return $this->pushSignal($machine->registration_id, 'redpacket_received');
    }

    public function pushAppMenuAnalysisSignal(Request $request)
    {
        return $this->jpush->push($request->registrationId, 'app_menu_analysis');
    }

    public function pushApiAnalysisSignal(Request $request)
    {
        return $this->jpush->push($request->registrationId, 'api_analysis');
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
        
        $response = $this->jpush->push($machine->registration_id, 'account_type', null, null, [], $request->account_type, $request->is_same_person);
        if ($response['http_code'] == static::CODE_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage('push to machine failed!');
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
        
        $response = $this->jpush->push($machine->registration_id, 'account_type', null, null, [], $request->account_type);
        if ($response['http_code'] == static::CODE_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage('push to machine failed!');
        }
    }

    private function pushSignal($registrationId, $sign)
    {
        if (!$registrationId) {
            //push signal to machines every hour
            $this->multiplePush($sign);
        } else {
            //push to machine by manual control
            return $this->singlePush($registrationId, $sign);
        }
    }

    private function multiplePush($sign)
    {
        $registrationIds = Machine::pluck('registration_id');
        foreach($registrationIds as $registrationId) {
            $machine = Machine::where('registration_id', $registrationId)->first();
            //心跳间隔超过30分钟，则认为机器未在线
            if (floor((strtotime(Carbon::now())-strtotime($machine->updated_at))%86400/60) > 30) {
                Log::error('Device '.$machine->device.' not online, not pushed!');
            } else {
                $pushed_at = Carbon::now()->timestamp;
                $response = $this->jpush->push($registrationId, $sign, $pushed_at);
                if ($response['http_code'] == static::CODE_SUCCESS) {
                    Log::info('Registration id: '.$registrationId.' pushed success!');
                    PushRecord::create([
                        'machine_id' => $machine->id,
                        'type' => $sign,
                        'pushed_at' => $pushed_at,
                    ]);
                } else {
                    Log::error('Registration id: '.$registrationId.' pushed fail!');
                }
            }
        }
    }

    private function singlePush($registrationId, $sign)
    {
        $machine = Machine::where('registration_id', $registrationId)->first();
        $pushed_at = Carbon::now()->timestamp;
        $response = $this->jpush->push($registrationId, $sign, $pushed_at);
        if ($response['http_code'] == static::CODE_SUCCESS) {
            Log::info('Registration id: '.$registrationId.' pushed success!');
            PushRecord::create([
                'machine_id' => $machine->id,
                'type' => $sign,
                'pushed_at' => $pushed_at,
            ]);
            return response()->json([
                'http_code' => static::CODE_SUCCESS
            ]);
        } else {
            Log::error('Registration id: '.$registrationId.' pushed fail!');
            return response()->json([
                'http_code' => static::CODE_ERROR,
                'msg' => '网络糟糕，获取信息失败！'
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
}

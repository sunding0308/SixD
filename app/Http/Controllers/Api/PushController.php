<?php

namespace App\Http\Controllers\Api;

use App\Machine;
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
        
        $response = $this->jpush->push($machine->registration_id, 'account_type', null, [], $request->account_type, $request->is_same_person);
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
        
        $response = $this->jpush->push($machine->registration_id, 'account_type', null, [], $request->account_type);
        if ($response['http_code'] == static::CODE_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage('push to machine failed!');
        }
    }

    private function pushSignal($registrationId, $sign)
    {
        if (!$registrationId) {  //push signal to machine every hour
            $registrationIds = Machine::pluck('registration_id');
            foreach($registrationIds as $registrationId) {
                $response = $this->jpush->push($registrationId, $sign);
                sleep(5);
                if ($response['http_code'] == static::CODE_SUCCESS) {
                    Log::info('Registration id: '.$registrationId.' pushed success!');
                    $res = $this->jpush->report((int)$response['body']['msg_id'], $registrationId);
                    if ($res['http_code'] == static::CODE_SUCCESS && $res['body'][$registrationId]['status'] == 0) {
                        Log::info('Registration id: '.$registrationId.' machine received success!');
                    } else {
                        Log::error('Registration id: '.$registrationId.' machine received fail!');
                    }
                } else {
                    Log::error('Registration id: '.$registrationId.' pushed fail!');
                }
            }
            return;
        } else {  //push to machine by manual control
            $response = $this->jpush->push($registrationId, $sign);
            if ($response['http_code'] == static::CODE_SUCCESS) {
                Log::info('Registration id: '.$registrationId.' pushed success!');
                while (empty($response['body']['msg_id'])) {
                    sleep(1);
                    Log::info('Sleep time: +1s');
                 }
                return  $this->jpush->report((int)$response['body']['msg_id'], $registrationId);
            } else {
                Log::error('Registration id: '.$registrationId.' pushed fail!');
                return response()->json([
                    'http_code' => 400,
                    'msg' => '网络糟糕，获取各余量失败！'
                    ]);
            }
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

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
    //紧急服务申请
    const URGENT_SERVICE_URL = 'https://tminiapps.sixdrops.com/outer/api/machine/urgentServiceTicket/urgentServiceTicketApply.do';
    //紧急服务完成
    const URGENT_SERVICE_COMPLETE_URL = 'https://tminiapps.sixdrops.com/outer/api/machine/urgentServiceTicket/urgentServiceTicketComplete.do';

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
    public function pushAlarmsSignal(Request $request)
    {
        return $this->pushSignal($request->registrationId, 'alarms');
    }

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
            'device' => 'required|exists:machines',
            'account_type' => 'required',
            'is_same_person' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();
        
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
            'device' => 'required|exists:machines',
            'account_type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseErrorWithMessage($validator->errors()->first());
        }

        $machine = Machine::where('device',$request->device)->first();
        
        $response = $this->jpush->push($machine->registration_id, 'account_type', null, [], $request->account_type);
        if ($response['http_code'] == static::CODE_SUCCESS) {
            return $this->responseSuccess();
        } else {
            return $this->responseErrorWithMessage('push to machine failed!');
        }
    }

    private function pushSignal($registrationId, $sign)
    {
        if (!$registrationId) {
            $registrationIds = Machine::pluck('registration_id');
            foreach($registrationIds as $registrationId) {
                $response = $this->jpush->push($registrationId, $sign);
                if ($response['http_code'] == static::CODE_SUCCESS) {
                    Log::info('Registration id: '.$registrationId.' pushed success!');
                } else {
                    Log::error('Registration id: '.$registrationId.' pushed fail!');
                }
            }
            return;
        } else {
            $response = $this->jpush->push($registrationId, $sign);
            if ($response['http_code'] == static::CODE_SUCCESS) {
                Log::info('Registration id: '.$registrationId.' pushed success!');
            } else {
                Log::error('Registration id: '.$registrationId.' pushed fail!');
            }
        }
    }

    /**
     * push to data cloud
     */
    public function pushAlarmsToDataCloud(Request $request)
    {
        Machine::with('alarm')->get()->map(function ($item, $key) {
            if ($item->hasAlarms()) {
                $response = $this->client->request('POST', self::URGENT_SERVICE_URL, [
                    'form_params' => [
                        'machineId' => $item->device,
                        'hotelName' => $item->installation->hotel_name,
                        'hotelId' => $item->installation->hotel_code,
                        'hotelAddress' => $item->installation->hotel_address,
                        'roomNo' => $item->installation->room,
                        'serviceContent' => collect($item->alarm)->except('id', 'machine_id', 'created_at', 'updated_at')->filter()->implode(',')
                    ]
                ]);
                if ($response->status == static::CODE_STATUS_SUCCESS) {
                    Log::info('Device '.$item->device.' pushed alarm to data cloud success!');
                } else {
                    Log::error('Device '.$item->device.' pushed alarm to data cloud failed!');
                }
            }
        });
    }

    public function pushAlarmsCompleteToDataCloud(Request $request)
    {
        $response = $this->client->request('POST', self::URGENT_SERVICE_COMPLETE_URL, [
            'form_params' => [
                'machineId' => $request->device,
                'serviceContent' => $request->service_content,
                'maintenanceStatus' => $request->maintenance_status
            ]
        ]);
        if ($response->status == static::CODE_STATUS_SUCCESS) {
            Log::info('Device '.$request->device.' pushed alarm complete to data cloud success!');
            return $this->responseSuccess();
        } else {
            Log::error('Device '.$request->device.' pushed alarm complete to data cloud failed!');
            return $this->responseErrorWithMessage('pushed alarm complete to data cloud failed!');
        }
    }
}

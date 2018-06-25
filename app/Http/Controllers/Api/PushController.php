<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Illuminate\Http\Request;
use App\Services\JPushService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\ApiController;

class PushController extends ApiController
{
    private $client;
    
    public function __construct(JPushService $client)
    {
        $this->client = $client;
    }

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
        return $this->client->push($request->registrationId, 'app_menu_analysis');
    }

    public function pushApiAnalysisSignal(Request $request)
    {
        return $this->client->push($request->registrationId, 'api_analysis');
    }

    private function pushSignal($registrationId, $sign)
    {
        if (!$registrationId) {
            $registrationIds = Machine::pluck('registration_id');
            foreach($registrationIds as $registrationId) {
                $response = $this->client->push($registrationId, $sign);
                if ($response['http_code'] == 200) {
                    Log::info('Registration id: '.$registrationId.' pushed success!');
                } else {
                    Log::error('Registration id: '.$registrationId.' pushed fail!');
                }
            }
            return;
        } else {
            return $this->client->push($registrationId, $sign);
        }
    }
}

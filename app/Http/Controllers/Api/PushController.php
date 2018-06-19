<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\JPushService;
use App\Http\Controllers\Controller;

class PushController extends Controller
{
    private $client;
    
    public function __construct(JPushService $client)
    {
        $this->client = $client;
    }

    public function pushTopupSignal(Request $request)
    {
        return $this->client->push($request->registrationId, 'topup');
    }

    public function pushAlarmsSignal(Request $request)
    {
        return $this->client->push($request->registrationId, 'alarms');
    }

    public function pushOverageSignal(Request $request)
    {
        return $this->client->push($request->registrationId, 'overage');
    }

    public function pushHardwareStatusSignal(Request $request)
    {
        return $this->client->push($request->registrationId, 'hardware_status');
    }

    public function pushRecordsSignal(Request $request)
    {
        return $this->client->push($request->registrationId, 'records');
    }

    public function pushEnvironmentSignal(Request $request)
    {
        return $this->client->push($request->registrationId, 'environment');
    }

    public function pushWaterQualityStatisticsSignal(Request $request)
    {
        return $this->client->push($request->registrationId, 'water_quality_statistics');
    }

    public function pushAppMenuAnalysisSignal(Request $request)
    {
        return $this->client->push($request->registrationId, 'app_menu_analysis');
    }

    public function pushApiAnalysisSignal(Request $request)
    {
        return $this->client->push($request->registrationId, 'api_analysis');
    }
}

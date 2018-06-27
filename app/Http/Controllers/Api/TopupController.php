<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\JPushService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\ApiController;

class TopupController extends ApiController
{
    public function topup(Request $request, JPushService $jpush)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            Machine::where('id',$machine->id)->update([
                'water_overage' => 7200,
                'oxygen_overage' => 0,
                'air_overage' => 0,
                'humidity_overage' => 0,
            ]);

            //push topup data to machine
            $response = $jpush->push($machine->registration_id, 'topup', $machine->device, [7200,0,0,0]);
            if ($response['http_code'] == 200) {
                Log::info('Device '.$request->device.' topup success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' topup error: '.$e->getMessage().' Line: '.$e->getLine());
            return $this->responseErrorWithMessage($e->getMessage());
        }
    }

    public function vipTopup(Request $request, JPushService $jpush)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            Machine::where('id',$machine->id)->update([
                'water_overage' => 0,
                'oxygen_overage' => 0,
                'air_overage' => 0,
                'humidity_overage' => 0,
            ]);

            $client = new Client();
            $response = $client->request('GET', 'sixd.local/api/topup', [
                'query' => ['device' => $request->device, 'vip_code' => $request->vip_code]
            ]);

            //push topup data to machine
            $response = $jpush->push($machine->registration_id, 'vip_topup', $machine->device, [$response]);
            if ($response['http_code'] == 200) {
                Log::info('Device '.$request->device.' vip topup success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' vip topup error: '.$e->getMessage().' Line: '.$e->getLine());
            return $this->responseErrorWithMessage($e->getMessage());
        }
    }

    public function resetOverage(Request $request, JPushService $jpush)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            Machine::where('id',$machine->id)->update([
                'water_overage' => 7200,
                'oxygen_overage' => 0,
                'air_overage' => 0,
                'humidity_overage' => 0,
            ]);

            //push reset data to machine
            $response = $jpush->push($machine->registration_id, 'reset', $machine->device, [7200,0,0,0]);
            if ($response['http_code'] == 200) {
                Log::info('Device '.$request->device.' reset success!');
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' reset error: '.$e->getMessage().' Line: '.$e->getLine());
            return $this->responseErrorWithMessage($e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Illuminate\Http\Request;
use App\Services\JPushService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\ApiController;

class TopupController extends ApiController
{
    public function topup(Request $request, JPushService $client)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            Machine::where('id',$machine->id)->update([
                'water_overage' => 4,
                'oxygen_overage' => 0,
                'air_overage' => 0,
                'humidity_overage' => 0,
            ]);

            //push topup data to machine
            $response = $client->push($machine->registration_id, 'topup', $machine->device, [4,0,0,0]);
            if ($response['http_code'] == 200) {
                Log::info('Device '.$request->device.' topup success!');
                return 'topup success!';
            }
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' topup error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TopupController extends Controller
{
    public function topup(Request $request)
    {
        try {
            $machine = Machine::where('device',$request->device)->first();
            Machine::where('id',$machine->id)->update([
                'water_overage' => 2,
                'oxygen_overage' => 0,
                'air_overage' => 0,
                'humidity_overage' => 0,
            ]);

            Log::info('Device '.$request->device.' topup success!');
            return redirect('/api/push_topup_signal?registrationId='.$machine->registration_id.
            '&water_overage=2&oxygen_overage=0&air_overage=0&humidity_overage=0');
        } catch (\Exception $e) {
            Log::error('Device '.$request->device.' topup error: '.$e->getMessage().' Line: '.$e->getLine());
        }
    }
}

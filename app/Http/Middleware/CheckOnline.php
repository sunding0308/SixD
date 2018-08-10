<?php

namespace App\Http\Middleware;

use Closure;
use App\Machine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckOnline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->registrationId) {
            $machine = Machine::where('registration_id', $request->registrationId)->first();
            if (floor((strtotime(Carbon::now())-strtotime($machine->updated_at))%86400/60) > 30) {
                Log::error('Device '.$machine->device.' not online, not pushed!');
                return response()->json([
                    'http_code' => 401,
                    'msg' => '设备未在线，请开启后尝试！'
                ]);
            }
        }
        return $next($request);
    }
}

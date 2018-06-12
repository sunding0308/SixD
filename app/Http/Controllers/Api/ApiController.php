<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function getStatisticsData(Request $request)
    {
        if($request->recent == 'three_month') {
            $recent = 90;
        } elseif($request->recent == 'one_year') {
            $recent = 365;
        } else {
            $recent = 30;
        }
        $machine = Machine::findOrFail($request->machineId);
        $waterQualityStatistics = $machine->waterQualityStatistics()->orderBy('created_at','desc')->take($recent)->get()
            ->sortBy('created_at')->values();
        return response()->json($waterQualityStatistics);
    }
}

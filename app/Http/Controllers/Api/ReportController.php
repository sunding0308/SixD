<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function hardwareStatus(Request $request)
    {
        return $request->data;
    }
    
    public function records(Request $request)
    {
        return $request->data;
    }

    public function environment(Request $request)
    {
        return $request->data;
    }

    public function waterQualityStatistics(Request $request)
    {
        return $request->data;
    }

    public function appMenuAnalysis(Request $request)
    {
        return $request->data;
    }

    public function apiAnalysis(Request $request)
    {
        return $request->data;
    }
}

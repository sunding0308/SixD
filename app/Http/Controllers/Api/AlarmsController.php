<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AlarmsController extends Controller
{
    public function alarms(Request $request)
    {
        return $request->data;
    }
}

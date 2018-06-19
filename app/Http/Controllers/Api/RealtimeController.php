<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RealtimeController extends Controller
{
    public function overage(Request $request)
    {
        return $request->data;
    }
}

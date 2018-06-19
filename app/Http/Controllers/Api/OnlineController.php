<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OnlineController extends Controller
{
    public function online(Request $request)
    {
        return $request->data;
    }
}

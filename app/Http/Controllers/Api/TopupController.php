<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TopupController extends Controller
{
    public function topup(Request $request)
    {
        return $request->data;
    }
}

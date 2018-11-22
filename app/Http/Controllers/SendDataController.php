<?php

namespace App\Http\Controllers;

use App\Services\IotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendDataController extends Controller
{
    public function send()
    {
        return view('send');
    }

    public function postSend(Request $request, IotService $iot)
    {
        $arr = explode(' ', $request->data);
        $newArr = [];
        foreach($arr as $i) {
            $newArr[] = chr(base_convert($i, 16, 10));
        }
        $newArr = implode('', $newArr);
        $response = $iot->rrpcToTest($request->device, $newArr);
        if ($response['Success']) {      
            session()->flash('success', '发送数据成功.');
        } else {
            session()->flash('error', '发送数据失败，请稍后再试！');
        }
        return back();
    }

    public function api()
    {
        return view('api');
    }
}

<?php

namespace App\Services;

use JPush\Client as JPush;
use Illuminate\Support\Facades\Log;

class JPushService
{
    private $app_key; // appkey
    private $master_secret; // master_secret
    private $client;

    public function __construct()
    {
        $this->app_key = config('jpush.app_key');
        $this->master_secret = config('jpush.master_secret');
        $this->client = new JPush($this->app_key, $this->master_secret, config('jpush.default_log_file')); // 实例化client.php中的client类
    }

    public function push($registrationId, $sign, $device=null, $overage=[], $account_type=null, $is_same_person=true, $show_redpacket=false)
    {
        $push_payload = $this->client->push() // 调用push方法（返回一个PushPayload实例）
            ->setPlatform('android') // 设置平台
            ->addRegistrationId($registrationId) // 设置设备推送
            ->message($sign, [
                'extras' => [
                  'device' => $device,
                  'overage' => $overage,
                  'account_type' => $account_type,
                  'is_same_person' => $is_same_person,
                  'show_redpacket' => $show_redpacket,
                ]
              ]); // 设置推送通知内容
        try {
            $response = $push_payload->send(); // 执行推送
            return $response; // 请求成功，返回信息
        }catch (\JPush\Exceptions\APIConnectionException $e) { // 请求异常
            Log::error($e);
        } catch (\JPush\Exceptions\APIRequestException $e) { // 回复异常
            Log::error($e);
        }

        return false; // 请求失败
    }

    //送达状态查询
    public function report($msgId, $registrationId)
    {
        $report = $this->client->report();
        $response = $report->getMessageStatus($msgId, $registrationId);
        return $response;
    }
}
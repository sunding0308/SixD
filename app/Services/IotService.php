<?php

namespace App\Services;

include_once __DIR__.'/../Libs/aliyun-openapi-php-sdk/aliyun-php-sdk-core/Config.php';
use DefaultProfile;
use DefaultAcsClient;
use \Iot\Request\V20170420 as Iot;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class IotService
{
    private $accessKeyID;
    private $accessSecret;
    private $productKey;
    private $client;

    public function __construct()
    {
        $this->accessKeyID = config('iot.accessKeyID');
        $this->accessSecret = config('iot.accessSecret');
        $this->productKey = config('iot.productKey');
        $iClientProfile = DefaultProfile::getProfile(config('iot.regionId'), $this->accessKeyID, $this->accessSecret);
        $this->client = new DefaultAcsClient($iClientProfile);
    }

    /**
     * Register device
     * @param $productKey
     * @param $deviceName
     */
    public function registDevice($deviceName)
    {
        $request = new Iot\RegistDeviceRequest();
        $request->setProductKey($this->productKey);
        $request->setDeviceName($deviceName);
        $response = $this->client->getAcsResponse($request);
        Log::info('Register status: '.$response->Success);

        if ($response->Success) {
            return [
                "data" => [
                    "DeviceName" => $response->DeviceName,
                    "DeviceSecret" => $response->DeviceSecret,
                    "ProductKey" => $this->productKey,
                ]
            ];
        } else {
            $res = $this->queryDeviceByName($deviceName);
            return $res;
        }
    }

    /**
     * Query device by devicename
     * @param $productKey
     * @param $deviceName
     */
    public function queryDeviceByName($deviceName)
    {
        $request = new Iot\QueryDeviceByNameRequest();
        $request->setProductKey($this->productKey);
        $request->setDeviceName($deviceName);
        $response = $this->client->getAcsResponse($request);
        Log::info('Query device by name status: '.$response->Success);

        if ($response->Success) {
            return [
                "data" => [
                    "DeviceName" => $response->DeviceInfo->DeviceName,
                    "DeviceSecret" => $response->DeviceInfo->DeviceSecret,
                    "ProductKey" => $this->productKey,
                ]
            ];
        }
    }

    /**
     * Send rrpc request
     * @param $productKey
     * @param $deviceName
     */
    public function rrpc($sign, $deviceName, $overage=[], $account_type=null, $is_same_person=true, $show_redpacket=false, $redpacket_qr_code=null)
    {
        $request = new Iot\RRpcRequest();
        //Base64 String
        $messageContent = base64_encode(json_encode([
            'message' => $sign,
            'extras' => [
                'device' => $deviceName,
                'overage' => $overage,
                'account_type' => $account_type,
                'is_same_person' => $is_same_person,
                'show_redpacket' => $show_redpacket,
                'redpacket_qr_code' => $redpacket_qr_code,
            ]
        ]));
        $request->setProductKey($this->productKey);
        $request->setDeviceName($deviceName);
        $request->setRequestBase64Byte($messageContent);
        $request->setTimeout(5000);
        $response = $this->client->getAcsResponse($request);
        Log::info($response->Code);

        if ($response->Success) {
            $payload = json_decode(base64_decode($response->PayloadBase64Byte));
            return [
                "Success" => $response->Success,
                "data" => [
                    "device" => optional($payload)->device,
                    "type" => optional($payload)->type,
                    "overage" => optional($payload)->overage,
                ]
            ];
        } else {
            return [
                "Success" => $response->Success,
            ];
        }
    }
}
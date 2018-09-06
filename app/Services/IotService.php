<?php

namespace App\Services;

include_once __DIR__.'/../Libs/aliyun-openapi-php-sdk/aliyun-php-sdk-core/Config.php';
use DefaultProfile;
use DefaultAcsClient;
use \Iot\Request\V20170420 as Iot;

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
    public function rrpc($deviceName)
    {
        $request = new Iot\RRpcRequest();
        //Base64 String
        $messageContent = base64_encode("{\"action\":\"unlock\"}");
        $request->setProductKey($this->productKey);
        $request->setDeviceName($deviceName);
        $request->setRequestBase64Byte($messageContent);
        $request->setTimeout(5000);
        $response = $this->client->getAcsResponse($request);
        
        return $response;
    }
}
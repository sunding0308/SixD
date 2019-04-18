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
     * Batch check device state
     * @param $deviceIds
     * @return array
     */
    public function BatchGetDeviceState($deviceIds)
    {
        $request = new Iot\BatchGetDeviceStateRequest();
        $request->setProductKey($this->productKey);
        $request->setDeviceNames($deviceIds);
        $response = $this->client->getAcsResponse($request);
        Log::info('Batch Query device state: '.$response->Success);

        if ($response->Success) {
            return [
                "success" => $response->Success,
                "DeviceStatusList" => $response->DeviceStatusList
            ];
        }
        return [
            "success" => $response->Success,
            "ErrorMessage" => $response->ErrorMessage
        ];
    }

    /**
     * Send rrpc request to water
     * @param $productKey
     * @param $deviceName
     */
    public function rrpcToWater($sign, $deviceName, $overage=[], $account_type=null, $is_same_person=true, $show_redpacket=false, $redpacket_qr_code=null)
    {
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

        $response = $this->rrpc($deviceName, $messageContent);

        if ($response->Success) {
            $payload = json_decode(base64_decode($response->PayloadBase64Byte));
            return [
                "Success" => $response->Success,
                "status" => optional($payload)->status,
                "message" => optional($payload)->message,
                "data" => [
                    "device" => optional($payload)->device,
                    "type" => optional($payload)->type,
                    "overage" => optional($payload)->overage,
                ]
            ];
        } else {
            Log::info($response->Code);
            return [
                "Success" => $response->Success,
            ];
        }
    }

    /**
     * Send rrpc request to vending
     */
    public function rrpcToVending($deviceName)
    {
        //Base64 String
        $messageContent = base64_encode(json_encode([
            'message' => 'status'
        ]));

        $response = $this->rrpc($deviceName, $messageContent);

        if ($response->Success) {
            $payload = json_decode(base64_decode($response->PayloadBase64Byte));
            return [
                "Success" => $response->Success,
                "signal" => optional($payload)->signal
            ];
        } else {
            Log::info($response->Code);
            return [
                "Success" => $response->Success,
            ];
        }
    }

    /**
     * Send rrpc request to oxygen and washing
     * @param $productKey
     * @param $deviceName
     */
    public function rrpcForClear($deviceName)
    {
        //Base64 String
        $messageContent = base64_encode(json_encode([
            'opt' => 'clear'
        ]));

        return $this->rrpc($deviceName, $messageContent);
    }

    /**
     * Send rrpc request to test
     * @param $productKey
     * @param $deviceName
     */
    public function rrpcToTest($deviceName, $data)
    {
        //Base64 String
        $messageContent = base64_encode($data);

        $response = $this->rrpc($deviceName, $messageContent);

        print_r($response);
        if ($response->Success) {
            print_r(base64_decode($response->PayloadBase64Byte));
        }
    }

    private function rrpc($deviceName, $messageContent)
    {
        $request = new Iot\RRpcRequest();
        $request->setProductKey($this->productKey);
        $request->setDeviceName($deviceName);
        $request->setRequestBase64Byte($messageContent);
        $request->setTimeout(5000);
        $response = $this->client->getAcsResponse($request);

        return $response;
    }
}
<?php

namespace Medboubazine\BinancePay\Core;

use Medboubazine\BinancePay\Core\Resources\Credentials;
use Medboubazine\BinancePay\Core\Traits\GuzzleHttpRequest;
use Illuminate\Support\Str;
use Medboubazine\BinancePay\Core\Helpers\DefaultHeaders;

class Certificates
{
    use GuzzleHttpRequest;

    /**
     * base_uri
     *
     * @var string
     */
    protected static $_base_uri = "https://bpay.binanceapi.com/binancepay/openapi/";
    /**
     * constructor
     */
    public static function get(Credentials $credentials)
    {
        $body_json = json_encode([]);
        //
        $options['headers'] = array_merge(DefaultHeaders::get($credentials, $body_json), [
            'Accept' => 'application/json; charset=utf-8',
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
        $options['body'] = $body_json;
        //send request
        $response = self::_g_sendRequest("POST", "certificates", $options);
        //
        $status_code = $response->getStatusCode();
        $response_array = json_decode($response->getBody()->getContents(), true);
        //
        if ($status_code == 200 && $response_array['status'] == 'SUCCESS') {
            $data = current($response_array['data']);
            //
            return [
                "cert_publickey" => $data["certPublic"],
                "cert_serial" => $data["certSerial"],
            ];
        }
        return [];
    }
    /**
     * get body Array
     *
     * @return array
     */
    public static function getBodyAsArray($credentials,  $order,  $product,  $urls, $buyer)
    {
        $env = [
            "terminalType" => $credentials->getEnvTerminalType(),
        ];
        if ($credentials->getEnvTOsType()) {
            $env["osType"] = $credentials->getEnvTOsType();
        }
        if ($credentials->getEnvOrderClientIp()) {
            $env["orderClientIp"] = $credentials->getEnvTOsType();
        }
        if ($credentials->getEnvCookieId()) {
            $env["cookieId"] = $credentials->getEnvCookieId();
        }
        //goods
        $goods = [
            "referenceGoodsId" => $product->getId(),
            "goodsType" => $product->getType(),
            "goodsCategory" => $product->getCategory(),
            "goodsName" => $product->getName(),
        ];
        //others
        $array = [
            "env" => $env,
            //
            "goods" => $goods,
            //
            //order
            "merchantTradeNo" => $order->getId(),
            "orderAmount" => $order->getAmount(),
            "currency" => $order->getCurrency(),
            //urls
            "returnUrl" => $urls->getReturnUrl(),
            "cancelUrl" => $urls->getCancelUrl(),
            "webhookUrl" => $urls->getWebhookUrl(),
        ];
        if ($buyer) {
            $array['buyer'] = [
                "buyerName" => [
                    "firstName" => $buyer->getFirstName(),
                    "lastName" => $buyer->getLastName(),
                ],
            ];
        }
        return $array;
    }
}

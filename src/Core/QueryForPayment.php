<?php

namespace Medboubazine\BinancePay\Core;

use Medboubazine\BinancePay\Core\Resources\Credentials;
use Medboubazine\BinancePay\Core\Traits\GuzzleHttpRequest;
use Medboubazine\BinancePay\Core\Helpers\DefaultHeaders;
use Medboubazine\BinancePay\Core\PayPayment;

class QueryForPayment
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
    public static function get(Credentials $credentials, $id)
    {
        if (!is_string($id)) {
            return null;
        }
        $body = self::getBodyAsArray($id);
        $body_json = json_encode($body);

        //
        $options['headers'] = array_merge(DefaultHeaders::get($credentials, $body_json), [
            'Accept' => 'application/json; charset=utf-8',
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
        $options['body'] = $body_json;
        //send request
        $response = self::_g_sendRequest("POST", "v2/order/query", $options);
        //
        $status_code = $response->getStatusCode();
        $response_array = json_decode($response->getBody()->getContents(), true);
        //
        if ($status_code == 200 && $response_array['status'] == 'SUCCESS') {
            $data = $response_array['data'];
            $timestamp = $data['createTime'] / 1000;
            return (new PayPayment())
                ->setId($data['merchantTradeNo'])
                ->setStatus($data['status'])
                ->setCurrency($data['currency'])
                ->setAmount($data['orderAmount'])
                ->setCreatedAt(date("Y-m-d H:i:s", $timestamp))
                ->setTimeZone(date("T", $timestamp));
        }
        return null;
    }
    /**
     * get body Array
     *
     * @return array
     */
    public static function getBodyAsArray(string $id)
    {
        return [
            "merchantTradeNo" => $id,
        ];
    }
}

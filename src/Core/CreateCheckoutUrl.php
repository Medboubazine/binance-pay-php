<?php

namespace Medboubazine\BinancePay\Core;

use Medboubazine\BinancePay\Core\Helpers\HashSignature;
use Medboubazine\BinancePay\Core\Resources\Buyer;
use Medboubazine\BinancePay\Core\Resources\Credentials;
use Medboubazine\BinancePay\Core\Resources\Order;
use Medboubazine\BinancePay\Core\Resources\Product;
use Medboubazine\BinancePay\Core\Resources\Urls;
use Medboubazine\BinancePay\Core\Traits\GuzzleHttpRequest;
use Illuminate\Support\Str;
use Medboubazine\BinancePay\Core\Helpers\DefaultHeaders;
use Medboubazine\BinancePay\Core\PayPayment;

class CreateCheckoutUrl
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
    public static function get(Credentials $credentials, Order $order, Product $product, Urls $urls, ?Buyer $buyer = null)
    {
        $body = self::getBodyAsArray($credentials,  $order,  $product,  $urls, $buyer);
        $body_json = json_encode($body);

        //
        $options['headers'] = array_merge(DefaultHeaders::get($credentials, $body_json), [
            'Accept' => 'application/json; charset=utf-8',
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
        $options['body'] = $body_json;
        //send request
        $response = self::_g_sendRequest("POST", "v2/order", $options);
        //
        $status_code = $response->getStatusCode();
        $response_array = json_decode($response->getBody()->getContents(), true);
        //
        if ($status_code == 200 && $response_array['status'] == 'SUCCESS') {
            $data = $response_array['data'];
            //
            $timestamp = $data['expireTime'] / 1000;
            return (new PayPayment())
                ->setPrepayId($data['prepayId'])
                ->setQrcodeLink($data['qrcodeLink'])
                ->setQrContent($data['qrContent'])
                ->setCheckoutUrl($data['checkoutUrl'])
                ->setDeeplink($data['deeplink'])
                ->setUniversalUrl($data['universalUrl'])
                ->setExpireTime(date("Y-m-d H:i:s", $timestamp))
                ->setTimeZone(date("T", $timestamp));
        }
        return null;
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
            "orderExpireTime" => $order->getExpireTime(),
            //urls
            "returnUrl" => $urls->getReturnUrl(),
            "cancelUrl" => $urls->getCancelUrl(),
            "webhookUrl" => $urls->getWebhookUrl(),
        ];
        //allowed currencies
        if ($order->getAllowedCurrencies()) {
            $array['supportPayCurrency'] = \implode(",", $order->getAllowedCurrencies());
        }
        //
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

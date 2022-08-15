<?php

namespace Medboubazine\BinancePay;

use Medboubazine\BinancePay\Core\Certificates;
use Medboubazine\BinancePay\Core\CreateCheckoutUrl;
use Medboubazine\BinancePay\Core\ParsePayWebhook;
use Medboubazine\BinancePay\Core\QueryForPayment;
use Medboubazine\BinancePay\Core\Resources\Buyer;
use Medboubazine\BinancePay\Core\Resources\Credentials;
use Medboubazine\BinancePay\Core\Resources\Order;
use Medboubazine\BinancePay\Core\Resources\Product;
use Medboubazine\BinancePay\Core\Resources\Urls;

class Binance
{
    /**
     * getCheckoutUrl
     *
     * @param Credentials $credentials
     * @param Order $order
     * @param Product $product
     * @param Urls $urls
     * @param Buyer|null $buyer
     * @return PayPayment|null
     */
    public function getCheckoutUrl(Credentials $credentials, Order $order, Product $product, Urls $urls, ?Buyer $buyer = null)
    {
        return CreateCheckoutUrl::get($credentials,  $order,  $product,  $urls, $buyer);
    }
    /**
     * Parse webhook notification
     *
     * @return bool
     */
    public function checkWebhook(Credentials $credentials)
    {
        $certificates = Certificates::get($credentials);
        return ParsePayWebhook::check($credentials, $certificates);
    }
    /**
     * Check
     *
     * @return string|null
     */
    public function getWebhookPayId()
    {
        return ParsePayWebhook::getPayId();
    }
    /**
     * getPayment
     *
     * @return PayPayment|null
     */
    public function getPayment(Credentials $credentials, $pay_id)
    {
        return QueryForPayment::get($credentials, $pay_id);
    }
}

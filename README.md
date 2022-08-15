# How To Use

- Redirect

```php

use Illuminate\Support\Carbon;
use Medboubazine\BinancePay\Binance;
use Medboubazine\BinancePay\Core\Resources\Credentials;
use Medboubazine\BinancePay\Core\Resources\Order;
use Medboubazine\BinancePay\Core\Resources\Product;
use Medboubazine\BinancePay\Core\Resources\Urls;

require "./vendor/autoload.php";

$date = new Carbon();
$timestamp = $date->addMinutes(20)->valueOf();
$timestamp = str_replace(".0", "", $timestamp);


$binance = new Binance();

//
$credentials = new Credentials();
$credentials->setApiKey("your_api_key")
    ->setApiSecret("your_api_secret")
    ->setEnvTerminalType("WEB");
//
$order = new Order();
$order->setId("test" . time())
    ->setAmount("10.22")
    ->setCurrency("USDT")
    ->setAllowedCurrencies(["BUSD", "BNB"])
    ->setExpireTime($timestamp);
//
$product = new Product;
$product->setId("steam-test-gift-card")
    ->setType("01")
    ->setCategory("6000")
    ->setName("Steam test gift card");
//
$urls = new Urls();
$urls->setReturnUrl("https://www.domain.com/back")
    ->setCancelUrl("https://www.domain.com/cancel")
    ->setWebhookUrl("https://www.domain.com/webhook");
//Create link
$payment = $binance->getCheckoutUrl($credentials,  $order,  $product,  $urls);
//redirect
$payment->getCheckoutUrl();

```

- Webhook

```php

use Medboubazine\BinancePay\Core\Resources\Credentials;

$credentials = new Credentials();
$credentials->setApiKey("your_api_key")
    ->setApiSecret("your_api_secret")
    ->setEnvTerminalType("WEB");

$status = $binance->checkWebhook($credentials);
$pay_id = $binance->getWebhookPayId();
$payment = $binance->getPayment($credentials, $pay_id);

```

- Query order

```php


use Medboubazine\BinancePay\Core\Resources\Credentials;

$credentials = new Credentials();
$credentials->setApiKey("your_api_key")
    ->setApiSecret("your_api_secret")
    ->setEnvTerminalType("WEB");

$payment = $binance->getPayment($credentials, "Order ID HERE");

```











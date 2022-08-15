<?php

namespace Medboubazine\BinancePay\Core\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

trait GuzzleHttpRequest
{
    /**
     * constructor
     */
    public static function _g_sendRequest(string $method, string $uri, array $options, array $client_configs = [])
    {
        $client_configs = array_merge(["base_uri" => self::$_base_uri ?? null,], $client_configs);
        //
        $client = new Client($client_configs);
        //
        $options = array_merge([
            'timeout'           => 15,
            'allow_redirects'   => false,
            'http_errors'       => false,
            'verify'       => false,
        ], $options);
        //
        $request = new Request($method, $uri, ($options['headers'] ?? []), ($options['body'] ?? ""));
        //
        return $client->sendAsync($request, $options)->wait();
    }
}

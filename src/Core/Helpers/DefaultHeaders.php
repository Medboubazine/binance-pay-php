<?php

namespace Medboubazine\BinancePay\Core\Helpers;

use Illuminate\Support\Str;
use Medboubazine\BinancePay\Core\Resources\Credentials;

class DefaultHeaders
{
    public static function get(Credentials $credentials, string $body = "")
    {
        $timestamp = \str_replace(".0", "", round(microtime(true) * 1000));
        //
        $nonce = bin2hex(openssl_random_pseudo_bytes(16));
        //
        $signature = Str::upper(HashSignature::make($timestamp . "\n" . $nonce . "\n" . $body . "\n", $credentials->getApiSecret()));

        return [
            'BinancePay-Certificate-SN' => $credentials->getApiKey(),
            'BinancePay-Timestamp' => $timestamp,
            'BinancePay-Nonce' => $nonce,
            'BinancePay-Signature' => $signature
        ];
    }
}

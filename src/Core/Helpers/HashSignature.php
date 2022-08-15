<?php

namespace Medboubazine\BinancePay\Core\Helpers;

class HashSignature
{
    /**
     * Create hash string
     *
     * @param string $string
     * @param string $key
     * @return string
     */
    public static function make(string $string, string $key)
    {
        return hash_hmac("sha512", $string, $key);
    }
}

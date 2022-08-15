<?php

namespace Medboubazine\BinancePay\Core;

use Medboubazine\BinancePay\Core\Resources\Credentials;

class ParsePayWebhook
{
    /**
     * Paypayment
     *
     * @var PayPayment|null
     */
    protected static ?string $pay_id;
    /**
     * Check
     *
     * @var boolean|null
     */
    protected static ?bool $check;
    /**
     * check
     *
     * @return bool
     */
    public static function check(Credentials $credentials, array $certificate)
    {
        //Certificate
        $certificate_public_key = $certificate["cert_publickey"] ?? "";
        $certificate_serial = $certificate["cert_serial"] ?? "";
        //
        if (empty($certificate_public_key)) {
            return false;
        }
        //Body
        $entityBody = file_get_contents("php://input");
        //Headers
        $headers = getallheaders();
        //
        $header_api_key_md5 = $headers["BinancePay-Certificate-SN"] ?? "";
        $header_nonce = $headers["BinancePay-Nonce"] ?? "";
        $header_timestamp = $headers["BinancePay-Timestamp"] ?? "";
        $header_signature = $headers["BinancePay-Signature"] ?? "";
        //
        $payload = $header_timestamp . "\n" . $header_nonce . "\n" . $entityBody . "\n";
        $decoded_signature = \base64_decode($header_signature);
        //verify
        $decoded_signature = $decoded_signature;
        //Check Signature
        self::$check = openssl_verify($payload, $decoded_signature, $certificate_public_key, OPENSSL_ALGO_SHA256) > 0;
        //parse body content
        $array = json_decode($entityBody, true);
        //parse data
        $data = ($array) ? json_decode($array['data'], true) : null;
        self::$pay_id = ($data) ? $data['merchantTradeNo'] : null;

        return self::$check;
    }
    /**
     * getPayId
     *
     * @return string|null
     */
    public static function getPayId()
    {
        return self::$pay_id ?? null;
    }
}

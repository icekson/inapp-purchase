<?php
/**
 * @author a.itsekson
 * @createdAt: 27.02.2017 16:12
 */

namespace Icekson\InAppPurchase\Apple;


use Icekson\InAppPurchase\Exception\VerificationException;
use Icekson\InAppPurchase\PaymentData;


class VerificationByApiCall extends \Icekson\InAppPurchase\Strategy\VerificationByApiCall
{

    const STATUS_OK = 0;
    const STATUS_ERROR_INVALID_JSON = 21000;
    const STATUS_ERROR_MISSING_RECEIPT = 21002;
    const STATUS_ERROR_RECEIPT_IS_NOT_AUTHENTICATED = 21003;
    const STATUS_ERROR_INVALID_SECRET = 21004;
    const STATUS_ERROR_SERVER_IS_NOT_AVAILABLE = 21005;
    const STATUS_ERROR_SUBSCRIPTION_IS_EXPIRED = 21006;
    const STATUS_ERROR_RECEIPT_FROM_TEST_ENVIRONMENT = 21007;
    const STATUS_ERROR_RECEIPT_FROM_PRODUCTION_ENVIRONMENT = 21008;

    private $serviceUrl = "";

    public function __construct(PaymentData $payload, $serviceUrl)
    {
        $this->payload = $payload;
        $this->serviceUrl = $serviceUrl;
    }

    public function verify(): bool
    {
        $receiptId = $this->payload->getPayload();
        $client = new \Guzzlehttp\Client();
        $response = $client->request("POST", $this->serviceUrl . "/verifyReceipt", [
            \GuzzleHttp\RequestOptions::BODY => json_encode(['receipt-data' => $receiptId])
        ]);

        $respBody = $response->getBody();
        $resp = json_decode($respBody->getContents());
        $res = !empty($resp) && $resp->status === self::STATUS_OK && !empty($resp->receipt->in_app) && isset($resp->receipt->in_app[0]->transaction_id) && $resp->receipt->in_app[0]->transaction_id == $this->payload->getTransactionId();

        if($res){
            $productId = !empty($resp->receipt->in_app[0]) ? $resp->receipt->in_app[0]->product_id : $this->payload->getProductId();
            $this->payload->setProductId($productId);
            $this->payload->setRawData($resp->receipt);
        }
        return $res;
    }

}
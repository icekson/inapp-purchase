<?php
/**
 * @author a.itsekson
 * @createdAt: 27.02.2017 16:12
 */

namespace Icekson\InAppPurchase\Apple;


use http\Exception\InvalidArgumentException;
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

    public function __construct(PaymentData $payload, $serviceUrl, $secret = null)
    {
        parent::__construct($payload, $secret);        
        $this->serviceUrl = $serviceUrl;
    }

    public function verify()
    {
        if($this->payload->hasErrors()){
            throw new \InvalidArgumentException(implode(", \n", $this->payload->getErrors()));
        }
        $receiptId = $this->payload->getPayload();
        $client = new \GuzzleHttp\Client();
        $response = $client->request("POST", $this->serviceUrl . "/verifyReceipt", [
            \GuzzleHttp\RequestOptions::BODY => json_encode(['receipt-data' => $receiptId, 'password' => $this->privateKey])
        ]);

        $respBody = $response->getBody();
        $resp = json_decode($respBody->getContents());
        $res = !empty($resp) && $resp->status === self::STATUS_OK && !empty($resp->receipt->in_app);

        if($res){
//            "1" - Customer canceled their subscription.
//            "2" - Billing error; for example customer’s payment information was no longer valid.
//            "3" - Customer did not agree to a recent price increase.
//            "4" - Product was not available for purchase at the time of renewal.
//            "5" - Unknown error.
            if(isset($resp->receipt->in_app[0]->expiration_intent) && (int)$resp->receipt->in_app[0]->expiration_intent > 0){
                return false;
            }
            $now = new \DateTime();
            usort($resp->receipt->in_app, function ($a, $b) {
                return $b->expires_date_ms - $a->expires_date_ms;
            });

            $inApp = array_filter($resp->receipt->in_app, function ($a) use ($now) {
                return $a->expires_date_ms < $now->getTimestamp()*1000;
            });
            if(empty($inApp)){
                return false;
            }
            foreach ($inApp as $item) {
                $this->payload->addProduct($item->product_id);
                $this->payload->addTransaction($item->transaction_id);
            }
            $this->payload->setRawData($resp->receipt);
        }
        return $res;
    }

}
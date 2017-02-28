<?php
namespace Icekson\InAppPurchase\Apple;

use Icekson\InAppPurchase\PaymentData;

class ApplePaymentData implements PaymentData
{
   protected $payload;

    protected $productId;

    protected $receiptData;

    protected $transactionId;

    protected $errors = [];

    /**
     * ApplePaymentData constructor.
     * @param $paymentData
     */
    private function  __construct($paymentData)
    {
        try {
            if (!$paymentData || empty($paymentData)) {
                throw new \InvalidArgumentException("Invalid response data, expected string");
            }

            $this->validateJSON($paymentData);
            $tmp = $paymentData;
            $this->payload = $tmp->Payload;
            $this->transactionId = $tmp->TransactionID;
        }catch (\InvalidArgumentException $ex){
            $this->errors[] = $ex->getMessage();
        }
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }


    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }



    private function validateJSON($json)
    {
        if (empty($json)) {
            throw new \InvalidArgumentException("Invalid or empty json is given");
        }

        if (!isset($json->Payload) || empty($json->Payload)) {
            throw new \InvalidArgumentException("Invalid parameter Payload");
        }
    }


    /**
     * @return string
     */
    public function toString(): string
    {
        return json_encode(["receipt-data" => $this->payload]);
    }

    /**
     * @param $str
     * @return PaymentData
     */
    static public function createFromJSON($str): PaymentData
    {
        return new ApplePaymentData($str);
    }

    public function getRawData()
    {
        return $this->receiptData;
    }

    public function setRawData($data)
    {
        $this->receiptData = $data;
    }

}

<?php
namespace Icekson\InAppPurchase\Apple;

use Icekson\InAppPurchase\PaymentData;
use Icekson\InAppPurchase\PaymentValidator;

class ApplePaymentData implements PaymentData
{
    protected $payload;

    protected $products = [];

    protected $receiptData;

    protected $transactions = [];

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
            $this->payload = $tmp->receipt;
        }catch (\InvalidArgumentException $ex){
            $this->errors[] = $ex->getMessage();
        }
    }

    public function getTransactions()
    {
        return $this->transactions;
    }

    public function getPlatformType()
    {
        return PaymentValidator::TYPE_APPLE_STORE;
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
	
	public function getPurchaseToken()
	{
		return $this->receiptData;
	}

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function addProduct($productId)
    {
        $this->products[] = $productId;
        return $this;
    }

    public function addTransaction($transactionId)
    {
        $this->transactions[] = $transactionId;
        return $this;
    }


    private function validateJSON($json)
    {
        if (empty($json)) {
            throw new \InvalidArgumentException("Invalid or empty json is given");
        }

        if (!isset($json->receipt) || empty($json->receipt)) {
            throw new \InvalidArgumentException("Invalid parameter receipt");
        }
    }


    /**
     * @return string
     */
    public function toString()
    {
        return json_encode(["receipt-data" => $this->payload]);
    }

    /**
     * @param $str
     * @return PaymentData
     */
    static public function createFromJSON($str)
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

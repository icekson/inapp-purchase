<?php
namespace Icekson\InAppPurchase\Apple;

use Icekson\InAppPurchase\PaymentData;

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
            $this->payload = $tmp->Payload;
        }catch (\InvalidArgumentException $ex){
            $this->errors[] = $ex->getMessage();
        }
    }

    public function getTransactions(): array
    {
        return $this->transactions;
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
    public function getProducts(): array
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

<?php

namespace Icekson\InAppPurchase\Google;
use Icekson\InAppPurchase\PaymentData;

/**
 * A representation of the data returned by the licensing service
 *
 */
class GooglePaymentData implements PaymentData
{
    const LICENSED = 0x0;
    const NOT_LICENSED = 0x1;
    const LICENSED_OLD_KEY = 0x2;
    const ERROR_NOT_MARKET_MANAGED = 0x3;
    const ERROR_SERVER_FAILURE = 0x4;
    const ERROR_OVER_QUOTA = 0x5;

    const ERROR_CONTACTING_SERVER = 0x101;
    const ERROR_INVALID_PACKAGE_NAME = 0x102;
    const ERROR_NON_MATCHING_UID = 0x103;

    /**
     * @var integer
     */
    protected $responseCode;

    /**
     * @var integer
     */
    protected $nonce;

    /**
     * @var string
     */
    protected $packageName;

    /**
     * @var string
     */
    protected $userId = "109030329278799942348"; // TODO: change to request parameter

    protected $products = [];

    /**
     * @var integer
     */
    protected $timestamp;

    protected $signature;

    protected $orderId;

    protected $transactions = [];
	
	protected $purchaseToken;

    protected $errors = [];

    /**
     * ResponseData constructor.
     * @param $responseData
     * @throws \InvalidArgumentException
     */
    private function __construct($responseData)
    {
        $data = $responseData;

        if (!$data || !isset($data->Payload)) {
            throw new \InvalidArgumentException("Invalid response data, data->Payload->json");
        }

        $jsonData = json_decode($data->Payload);
        $tmp = json_decode($jsonData->json);
        if (empty($tmp->orderId)) {
            if (!empty($data->TransactionID)) {
                $tmp->orderId = $data->TransactionID;
            } else {
                $tmp->orderId = $tmp->purchaseTime;
            }
        }
        try {
            $this->validateJSON($tmp);

            $this->addTransaction($data->TransactionID);
            $this->responseCode = $tmp->purchaseState;
            $this->purchaseToken = $tmp->purchaseToken;
            $this->packageName = $tmp->packageName;
            $this->addProduct($tmp->productId);
            $this->timestamp = $tmp->purchaseTime;
            $this->orderId = $tmp->orderId;

            $this->signature = $jsonData->signature;

        } catch (\InvalidArgumentException $ex) {
            $this->errors[] = $ex->getMessage();
        }
    }

    private function validateJSON($json)
    {
        if (empty($json)) {
            throw new \InvalidArgumentException("Invalid parameters format");
        }

        if (!isset($json->purchaseState)) {
            throw new \InvalidArgumentException("Invalid parameter responseCode");
        }
        if (!isset($json->purchaseToken) || empty($json->purchaseToken)) {
            throw new \InvalidArgumentException("Invalid parameter purchaseToken");
        }

        if (!isset($json->packageName) || empty($json->packageName)) {
            throw new \InvalidArgumentException("Invalid parameter packageName");
        }

        if (!isset($json->productId) || empty($json->productId)) {
            throw new \InvalidArgumentException("Invalid parameter productId");
        }

        if (!isset($json->orderId) || empty($json->orderId)) {
            throw new \InvalidArgumentException("Invalid parameter orderId");
        }

        if (!isset($json->purchaseTime) || empty($json->purchaseTime)) {
            throw new \InvalidArgumentException("Invalid parameter timestamp");
        }

    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param mixed $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }


    /**
     * Get the license status or error code
     *
     * @return integer
     */
    public function getResponseCode()
    {
        return (int)$this->responseCode;
    }

    /**
     * Get one-time nonce
     *
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }
	
	/**
     * Get one-time nonce
     *
     * @return string
     */
    public function getPurchaseToken()
    {
        return $this->purchaseToken;
    }

    /**
     * Get the application package name
     *
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * Get the response timestamp
     *
     * @return double
     */
    public function getTimestamp()
    {
        return (double)$this->timestamp;
    }

    /**
     * If server response was licensed
     *
     * @return bool
     */
    public function isLicensed()
    {
        return (self::LICENSED == $this->responseCode
            || self::LICENSED_OLD_KEY == $this->responseCode);
    }

    public function getPayload()
    {
        return $this->getRawData();
    }


    /**
     * @return string
     */
    public function toString(): string
    {
        return json_encode($this->getPayload());
    }


    /**
     * @param $str
     * @return GooglePaymentData
     */
    public static function createFromJSON($str): PaymentData
    {
        return new GooglePaymentData($str);
    }

    public function getRawData()
    {
        return [
            'productId' => $this->products[0],
            'orderId' => $this->orderId,
            'packageName' => $this->packageName,
            'purchaseState' => $this->responseCode,
            'purchaseToken' => $this->purchaseToken,
            'purchaseTime' => $this->timestamp,
            'signature' => $this->signature
        ];
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
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


}

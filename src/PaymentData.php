<?php
/**
 * @author a.itsekson
 * @createdAt: 01.04.2016 15:50
 */

namespace Icekson\InAppPurchase;


interface PaymentData
{
    public function toString();

    /**
     * @param $str
     * @return PaymentData
     */
    static public function createFromJSON($str);

    /**
     * @return string
     */
    public function getPlatformType();

    /**
     * @return array
     */
    public function getProducts();

    public function getRawData();
    public function setRawData($data);


    /**
     * @return array
     */
    public function getTransactions();
	
	public function getPurchaseToken();

    public function getPayload();

    public function addProduct($productId);

    public function addTransaction($transactionId);

    public function isSubscription();

    public function setExpirationTime($time);

    public function getExpirationTime();

    public function getPrice();

    public function getPurchaseTime();

    public function hasErrors();

    public function getErrors();

}
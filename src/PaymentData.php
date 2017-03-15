<?php
/**
 * @author a.itsekson
 * @createdAt: 01.04.2016 15:50
 */

namespace Icekson\InAppPurchase;


interface PaymentData
{
    public function toString(): string;

    static public function createFromJSON($str): PaymentData;

    public function getProductId();

    public function getRawData();

    public function getTransactionId();
	
	public function getPurchaseToken();

    public function getPayload();
}
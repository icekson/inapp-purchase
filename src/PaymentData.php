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

    public function getProducts(): array;

    public function getRawData();

    public function getTransactions(): array;
	
	public function getPurchaseToken();

    public function getPayload();

    public function addProduct($productId);

    public function addTransaction($transactionId);
}
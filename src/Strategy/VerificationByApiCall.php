<?php
/**
 * @author a.itsekson
 * @createdAt: 27.02.2017 15:44
 */

namespace Icekson\InAppPurchase\Strategy;


use Icekson\InAppPurchase\Exception\VerificationException;
use Icekson\InAppPurchase\PaymentData;
use Icekson\InAppPurchase\ResponseData;
use Icekson\InAppPurchase\VerificationInterface;

abstract class VerificationByApiCall implements VerificationStrategy
{

    /**
     * @var PaymentData|null
     */
    protected $payload = null;
    protected $privateKey = "";


    public function __construct(PaymentData $paymentData, $privateKey)
    {
        $this->payload = $paymentData;
        $this->privateKey = $privateKey;
    }

    /**
     * @return bool
     * @throws VerificationException
     */
    abstract public function verify(): bool;


}
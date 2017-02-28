<?php
/**
 * @author a.itsekson
 * @createdAt: 27.02.2017 15:43
 */

namespace Icekson\InAppPurchase\Strategy;


use Icekson\InAppPurchase\PaymentData;
use Icekson\InAppPurchase\VerificationInterface;

interface VerificationStrategy extends VerificationInterface
{

    const TYPE_BY_SIGNATURE = "signature";
    const TYPE_BY_API_CALL = "api_call";


}
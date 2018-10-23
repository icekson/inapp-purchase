<?php
/**
 * @author a.itsekson
 * @createdAt: 27.02.2017 15:36
 */

namespace Icekson\InAppPurchase;


interface PaymentDataContainerInterface{

    /**
     * @return PaymentData
     */
    public function getPaymentData();
}
<?php
/**
 * @author a.itsekson
 * @createdAt: 01.04.2016 16:12
 */

namespace Icekson\InAppPurchase;

use Icekson\InAppPurchase\Google\GooglePaymentData;
use Icekson\InAppPurchase\Apple\ApplePaymentData;
use Icekson\InAppPurchase\Exception\VerificationException;
use Icekson\InAppPurchase\Strategy\VerificationStrategy;
use Icekson\Utils\Logger;
use Psr\Log\LoggerInterface;

class PaymentValidator implements VerificationInterface
{

    const TYPE_GOOGLE_PLAY = "GooglePlay";
    const TYPE_APPLE_STORE = "AppleAppStore";

    private $paymentData;
    protected $logger;

    /**
     * @var null|VerificationStrategy
     */
    protected $verificationStrategy = null;

    public function __construct($paymentData, VerificationStrategy $strategy = null)
    {
        if ($paymentData instanceof PaymentData) {
            $this->paymentData = $paymentData;
        } else {

            if(!isset($paymentData->Store) || !in_array($paymentData->Store, [self::TYPE_GOOGLE_PLAY, self::TYPE_APPLE_STORE])){
                throw new \InvalidArgumentException("Invalid Store type is given");
            }

            $this->paymentData = self::createPaymentDataForPlatform($paymentData->Store, $paymentData);
        }
        $this->verificationStrategy = $strategy;
        $this->logger = null;
    }

    /**
     * @return null
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }



    /**
     * @param $strategy
     * @return $this
     */
    public function setStrategy(VerificationStrategy $strategy)
    {
        $this->verificationStrategy = $strategy;
        return $this;
    }


    /**
     * @return bool
     * @throws VerificationException
     */
    public function verify(): bool
    {

        if($this->verificationStrategy === null){
            throw new VerificationException("Verification strategy isn't specified");
        }
        $res = $this->verificationStrategy->verify($this->paymentData);

        if($this->logger !== null) {
            if ($res) {
                $this->logger->info("verification of purchase is successfully passed, payload: " . json_encode($this->paymentData->getRawData()));
            } else {
                $this->logger->warn("verification of purchase isn't passed, payload: " . json_encode($this->paymentData->getRawData()));
            }
        }
        return $res;

    }

    /**
     * @return PaymentData
     */
    public function getPaymentData(): PaymentData
    {
        return $this->paymentData;
    }

    /**
     * @param $type
     * @param $data
     * @return PaymentData
     */
    static private function createPaymentDataForPlatform($type, $data): PaymentData
    {
        $res = null;
        switch($type){
            case self::TYPE_APPLE_STORE:
                $res = ApplePaymentData::createFromJSON($data);
                break;
            case self::TYPE_GOOGLE_PLAY :
                $res = GooglePaymentData::createFromJSON($data);
                break;
        }
        if($res === null){
            throw new \InvalidArgumentException("Invalid platform are specified");
        }
        return $res;
    }

}
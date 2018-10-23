<?php
/**
 * @author a.itsekson
 * @createdAt: 28.02.2017 11:44
 */

namespace Icekson\InAppPurchase\Config;


use Icekson\Config\ConfigureInterface;
use Icekson\InAppPurchase\Config\AppleVerificationConfig;
use Icekson\InAppPurchase\Config\GoogleVerificationConfig;
use Icekson\InAppPurchase\Config\VerificationConfig;
use Icekson\InAppPurchase\PaymentValidator;

class Factory
{


    /**
     * @param $type
     * @param ConfigureInterface $config
     * @return VerificationConfig
     */
    static public function createPlatformConfig($type, ConfigureInterface $config)
    {
        $res = null;
        $inAppConfig = $config->get("inapp_purchase");
        switch($type){
            case PaymentValidator::TYPE_APPLE_STORE:
                $appleOptions = isset($inAppConfig["apple"]) ? $inAppConfig["apple"] : [];
                $res = new AppleVerificationConfig($appleOptions);
                break;
            case PaymentValidator::TYPE_GOOGLE_PLAY :
                $googleOptions = isset($inAppConfig["google"]) ? $inAppConfig["google"] : [];
                $res = new GoogleVerificationConfig($googleOptions);
                break;
        }
        if($res === null){
            throw new \InvalidArgumentException("Invalid platform are specified");
        }
        return $res;
    }

}
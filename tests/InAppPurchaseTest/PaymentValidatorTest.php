<?php
/**
 * @author a.itsekson
 * @createdAt: 28.02.2017 12:27
 */

namespace Icekson\InAppPurchaseTest;


use Icekson\Config\ConfigAdapter;
use Icekson\InAppPurchase\Config\AppleVerificationConfig;
use Icekson\InAppPurchase\Config\Factory;
use Icekson\InAppPurchase\Config\GoogleVerificationConfig;
use Icekson\InAppPurchase\Google\VerificationByApiCall;
use Icekson\InAppPurchase\PaymentValidator;

class PaymentValidatorTest extends \PHPUnit\Framework\TestCase
{
    private $config = null;

    protected function setUp()
    {
        $this->config = new ConfigAdapter(PATH_ROOT . "config.json");
    }

    public function testValidGoogleByApiCall()
    {
        $validPayloadStr = file_get_contents(PATH_ROOT . "data/valid_google_purchase.json");
        $payload = json_decode($validPayloadStr);

        /** @var GoogleVerificationConfig $config */
        $config = Factory::createPlatformConfig(PaymentValidator::TYPE_GOOGLE_PLAY, $this->config);

        $validator = new PaymentValidator($payload);
        $paymentData = $validator->getPaymentData();
        $strategy = new VerificationByApiCall($paymentData, $config->getPrivateAccessKey());
        $validator->setStrategy($strategy);

        $res = $validator->verify();

        $this->assertTrue($res, "Verification is not passed");
    }

    public function testInvalidGoogleByApiCall()
    {
        $validPayloadStr = file_get_contents(PATH_ROOT . "data/invalid_google_purchase.json");
        $payload = json_decode($validPayloadStr);

        /** @var GoogleVerificationConfig $config */
        $config = Factory::createPlatformConfig(PaymentValidator::TYPE_GOOGLE_PLAY, $this->config);

        $validator = new PaymentValidator($payload);
        $paymentData = $validator->getPaymentData();
        $strategy = new VerificationByApiCall($paymentData, $config->getPrivateAccessKey());
        $validator->setStrategy($strategy);

        $res = $validator->verify();

        $this->assertFalse($res, "Verification is passed");
    }



    public function testValidAppleByApiCall()
    {
        $validPayloadStr = file_get_contents(PATH_ROOT . "data/valid_apple_purchase.json");
        $payload = json_decode($validPayloadStr);

        /** @var AppleVerificationConfig $config */
        $config = Factory::createPlatformConfig(PaymentValidator::TYPE_APPLE_STORE, $this->config);

        $validator = new PaymentValidator($payload);
        $paymentData = $validator->getPaymentData();
        $strategy = new \Icekson\InAppPurchase\Apple\VerificationByApiCall($paymentData, $config->getVerificationApiUrl());
        $validator->setStrategy($strategy);

        $res = $validator->verify();

        $this->assertTrue($res, "Verification is not passed");
    }


    public function testInvalidAppleByApiCall()
    {
        $validPayloadStr = file_get_contents(PATH_ROOT . "data/invalid_apple_purchase.json");
        $payload = json_decode($validPayloadStr);

        /** @var AppleVerificationConfig $config */
        $config = Factory::createPlatformConfig(PaymentValidator::TYPE_APPLE_STORE, $this->config);

        $validator = new PaymentValidator($payload);
        $paymentData = $validator->getPaymentData();
        $strategy = new \Icekson\InAppPurchase\Apple\VerificationByApiCall($paymentData, $config->getVerificationApiUrl());
        $validator->setStrategy($strategy);

        $res = $validator->verify();

        $this->assertFalse($res, "Verification passed");
    }
}
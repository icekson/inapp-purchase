<?php
/**
 * @author a.itsekson
 * @createdAt: 28.02.2017 13:51
 */

namespace Icekson\InAppPurchaseTest\Config;


use Icekson\Config\ConfigAdapter;
use Icekson\InAppPurchase\Config\GoogleVerificationConfig;
use Webmozart\Assert\Assert;

class GoogleVerificationConfigTest extends  \PHPUnit\Framework\TestCase
{
    public function testConfigCreating()
    {
        $options = [
            "package_name" => "some_package",
            "public_key" => "public_key",
            "api_access_key" => "some_key",
            "verification_type" => GoogleVerificationConfig::VERIFICATION_TYPE_API,
            "signature_algorithm" => OPENSSL_ALGO_SHA256
        ];

        $config = new GoogleVerificationConfig($options);

        $this->assertEquals($config->getPackageName(), "some_package");
        $this->assertNotNull($config->getPublicKey());
        $this->assertEquals($config->getSignatureAlgorithm(), OPENSSL_ALGO_SHA256);
        $this->assertEquals($config->getPrivateAccessKey(), "some_key");
        $this->assertEquals($config->getVerificationType(), GoogleVerificationConfig::VERIFICATION_TYPE_API);

    }
}
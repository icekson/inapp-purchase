<?php
/**
 * @author a.itsekson
 * @createdAt: 27.02.2017 14:43
 */

namespace Icekson\InAppPurchase\Config;


class GoogleVerificationConfig extends VerificationConfig
{
    const VERIFICATION_TYPE_SIGN = "sign";
    const VERIFICATION_TYPE_API = "api";

    const KEY_PREFIX = "-----BEGIN PUBLIC KEY-----\n";
    const KEY_SUFFIX = '-----END PUBLIC KEY-----';

    private $packageName = "";

    private $publicKey = "";

    private $privateAccessKey = "";

    private $signAlgorithm = OPENSSL_ALGO_SHA256;

    private $verificationType = self::VERIFICATION_TYPE_API;


    private $mode = self::PURCHASE_MODE_PRODUCTION;


    public function __construct($options)
    {
        $this->fromArray($options);
    }

    /**
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }


    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @return string
     */
    public function getPrivateAccessKey()
    {
        return $this->privateAccessKey;
    }

    /**
     * @return string
     */
    public function getVerificationType()
    {
        return $this->verificationType;
    }



    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return int
     */
    public function getSignatureAlgorithm()
    {
        return $this->signAlgorithm;
    }


    public function fromArray(array $options)
    {
        parent::fromArray($options);

        $this->packageName = isset($options["package_name"]) ? $options["package_name"] : null;
        $this->privateAccessKey = isset($options["api_access_key"]) ? $options["api_access_key"] : null;
        $this->verificationType = isset($options["verification_type"]) ? $options["verification_type"] : null;
        $this->signAlgorithm = isset($options["signature_algorithm"]) ? $options["signature_algorithm"] : OPENSSL_ALGO_SHA256;

        $publicKey = isset($options["public_key"]) ? $options["public_key"] : null;
        $key = self::KEY_PREFIX . chunk_split($publicKey, 64, "\n") . self::KEY_SUFFIX;
        $key = openssl_get_publickey($key);
        $this->publicKey = $key;
    }

    public function toArray()
    {
        return [
            "package_name" => $this->packageName,
            "private_access_key" => $this->privateAccessKey,
            "public_key" => $this->publicKey,
            "verification_type" => $this->verificationType
        ];
    }


}
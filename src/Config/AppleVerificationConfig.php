<?php
/**
 * @author a.itsekson
 * @createdAt: 27.02.2017 14:43
 */

namespace Icekson\InAppPurchase\Config;

use Icekson\Config\ConfigureInterface;

class AppleVerificationConfig extends VerificationConfig
{

    private $serices = [
        self::PURCHASE_MODE_PRODUCTION => "https://buy.itunes.apple.com",
        self::PURCHASE_MODE_SANDBOX => "https://sandbox.itunes.apple.com",
    ];

    private $verificationApiUrl = "";

    private $mode = self::PURCHASE_MODE_PRODUCTION;

    public function __construct($options)
    {
        $this->fromArray($options);
    }

    /**
     * @return mixed|string
     */
    public function getVerificationApiUrl()
    {
        return $this->verificationApiUrl;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    public function fromArray(array $options)
    {
        parent::fromArray($options);

        $this->mode = isset($options["mode"]) ? $options["mode"] : self::PURCHASE_MODE_PRODUCTION;
        $this->verificationApiUrl = $this->serices[$this->mode];
    }

    public function toArray()
    {
        return [
            "mode" => $this->mode,
            "verification_api_url" => $this->verificationApiUrl
        ];
    }


}
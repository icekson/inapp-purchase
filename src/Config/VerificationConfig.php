<?php
/**
 * @author a.itsekson
 * @createdAt: 27.02.2017 14:43
 */

namespace Icekson\InAppPurchase\Config;

use Icekson\Config\ConfigureInterface;

abstract class VerificationConfig implements ConfigureInterface
{


    const PURCHASE_MODE_SANDBOX = "sandbox";
    const PURCHASE_MODE_PRODUCTION = "production";

    protected $options = [];

    public function get($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    abstract public function toArray();

    public function fromArray(array $data)
    {
        $this->options = $data;
        return $this;
    }

}
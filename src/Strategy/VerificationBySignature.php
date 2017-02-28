<?php
/**
 * @author a.itsekson
 * @createdAt: 27.02.2017 15:44
 */

namespace Icekson\InAppPurchase\Strategy;


use Icekson\InAppPurchase\Exception\VerificationException;
use Icekson\InAppPurchase\PaymentData;
use Icekson\InAppPurchase\VerificationInterface;

// TODO: verification by signature isn't work, need to change implementation
// TODO: use VerificationByApiCall instead
class VerificationBySignature implements VerificationStrategy
{

    /**
     * @var PaymentData|null
     */
    private $payload = null;
    private $publicKey = "";
    private $signature = null;
    private $signatureAlgorithm = OPENSSL_ALGO_SHA256;

    /**
     * VerificationBySignature constructor.
     * @param PaymentData $payload
     * @param $signature
     * @param $publicKey
     * @param int $signatureAlgorithm
     */
    public function __construct($signature, $publicKey, $signatureAlgorithm = OPENSSL_ALGO_SHA256)
    {
        $this->signature = $signature;
        $this->publicKey = $publicKey;
        $this->signatureAlgorithm = $signatureAlgorithm;
    }

    /**
     * @return bool
     * @throws VerificationException
     */
    public function verify(): bool
    {
        $payload = $this->payload->toString();
        $signature = str_replace('\\', '', $this->signature);

        $result = openssl_verify($payload, base64_decode($signature),
            $this->publicKey, $this->signatureAlgorithm);

        $res = true;
        //openssl_verify returns 1 for a valid signature
        if ($result === 0) {
            $res = false;
        } else if ($result !== 1) {
            throw new VerificationException(
                'Unknown error verifying the signature in openssl_verify');
        }
        return $res;
    }

}
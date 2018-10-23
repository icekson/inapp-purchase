<?php
/**
 * @author a.itsekson
 * @createdAt: 27.02.2017 16:12
 */

namespace Icekson\InAppPurchase\Google;


use Icekson\InAppPurchase\Exception\VerificationException;

class VerificationByApiCall extends \Icekson\InAppPurchase\Strategy\VerificationByApiCall
{


    public function verify()
    {
        if (!$this->payload->isLicensed()) {
            return false;
        }

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $this->privateKey);
        $client = new \Google_Client();
        $client->useApplicationDefaultCredentials();

        $client->addScope(\Google_Service_AndroidPublisher::ANDROIDPUBLISHER);

        try{
            $inapp = new \Google_Service_AndroidPublisher($client);
            $data = $inapp->purchases_products->get($this->payload->getPackageName(), $this->payload->getProducts()[0], $this->payload->getPurchaseToken());
            // check is purchase is valid
            if($data->getPurchaseState() != 0){
                return false;
            }
        }catch(\Google_Service_Exception $ex){
            return false;
        }
        return true;
    }

}
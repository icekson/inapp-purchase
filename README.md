###OVERVIEW

Main purpose of current library is verification of in-app purchases from GooglePlay and AppStore on server side

###USAGE

To verify purchase from GooglePlay we will be use `google/apiclient` package. First you need to create service account in google console, for more details see the following topic https://developers.google.com/api-client-library/php/auth/service-accounts#creatinganaccount

When you configured service account you will be able to verify purchases via `https://www.googleapis.com/auth/androidpublisher` API as the following:
```php

use Icekson\Config\ConfigAdapter;
use Icekson\InAppPurchase\Config\AppleVerificationConfig;
use Icekson\InAppPurchase\Config\Factory;
use Icekson\InAppPurchase\Config\GoogleVerificationConfig;
use Icekson\InAppPurchase\Google\VerificationByApiCall;
use Icekson\InAppPurchase\PaymentValidator;

$config = new ConfigAdapter("config.json");
$validPayloadStr = file_get_contents(__DIR__ . "/data/valid_google_purchase.json"); // payload data from GooglePlay, (you can find examples of purchase's payloads in `tests/data` folder)
$payload = json_decode($validPayloadStr);

/** @var GoogleVerificationConfig $config */
$platformConfig = Factory::createPlatformConfig(PaymentValidator::TYPE_GOOGLE_PLAY, $config);

$validator = new PaymentValidator($payload);
$paymentData = $validator->getPaymentData();
$strategy = new VerificationByApiCall($paymentData, $platformConfig->getPrivateAccessKey()); // privateAccessKey is a path to json file that you should download 
                                                                                            //from GoogleConsole during service account's creating
$validator->setStrategy($strategy);

if($validator->verify()){
    // purchase is successfully verified
}else{
    // purchase is invalid or fake
}
```

Example of Apple purchase verification:
```php

$config = new ConfigAdapter("config.json");
$validPayloadStr = file_get_contents(__DIR__."/data/valid_apple_purchase.json");
$payload = json_decode($validPayloadStr);

/** @var AppleVerificationConfig $config */
$platformConfig = Factory::createPlatformConfig(PaymentValidator::TYPE_APPLE_STORE, $config);

$validator = new PaymentValidator($payload);
$paymentData = $validator->getPaymentData();
$strategy = new \Icekson\InAppPurchase\Apple\VerificationByApiCall($paymentData, $platformConfig->getVerificationApiUrl());
$validator->setStrategy($strategy);

if($validator->verify()){
    // purchase is successfully verified
}else{
    // purchase is invalid or fake
}
```

#### Config structure

```json
{
  "inapp_purchase": {
    "google":{
      "package_name": "package name of your appclication",
      "public_key": "... public key for your project, given from Google Console ...",
      "api_access_key": "./data/keys/google_android_developer_api.json", /*path to private key configuration, given from Google Console*/ 
      "verification_type": "api",
      "signature_algorithm": 7 /*sha256, used for VerificationBySignature strategy only*/

    },
    "apple": {
      "mode": "production" // "production" or "sandbox"
    }
  }
}
```


 


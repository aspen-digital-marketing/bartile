<?php
 require("vendor/autoload.php");
 
 require_once(__DIR__ . '/vendor/autoload.php');

 // Configure API key authorization: api-key
 $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
 // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
 // $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('api-key', 'Bearer');
 // Configure API key authorization: partner-key
 $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('partner-key', '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');
 // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
 // $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('partner-key', 'Bearer');
 
 $apiInstance = new SendinBlue\Client\Api\AccountApi(
     // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
     // This is optional, `GuzzleHttp\Client` will be used as default.
     new GuzzleHttp\Client(),
     $config
 );
 
 try {
     $result = $apiInstance->getAccount();
     print_r($result);
 } catch (Exception $e) {
     echo 'Exception when calling AccountApi->getAccount: ', $e->getMessage(), PHP_EOL;
 }
            
?>
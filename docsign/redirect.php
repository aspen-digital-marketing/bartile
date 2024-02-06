<?php

require_once 'vendor/autoload.php';

use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\Tabs;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\Recipients;
use DocuSign\eSign\Configuration;
$authorizationCode = $_GET['code'];

$clientId = 'b2ea1aaf-ba96-4d5d-8d3f-76e633a852ce';
$clientSecret = 'b7ceaea6-5b42-4f64-982c-5485482b5e3f';
$redirectUri = 'https://bartile.goaspendigital.com/docsign/';


$config = new Configuration();


// Build the POST data
$postData = [
    'grant_type' => 'authorization_code',
    'code' => $authorizationCode,
    'redirect_uri' => $redirectUri,
];


$headers = [
    'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret)
];


$ch = curl_init('https://account-d.docusign.com/oauth/token');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);


$responseData = json_decode($response, true);

if (isset($responseData['access_token'])) {
    $access_token = $responseData['access_token'];
    $config->setHost('https://demo.docusign.net/restapi');

    $config->addDefaultHeader('Authorization', 'Bearer ' . $access_token);
    $apiClient = new ApiClient($config);

    // $apiClient->setHost('https://demo.docusign.net');
    // $apiClient->getOAuth()->setAccessToken($accessToken);
    
    // Create an envelope definition
    $envelopeDefinition = new EnvelopeDefinition();
    $envelopeDefinition->setEmailSubject('Please sign this document');
    $envelopeDefinition->setDocuments([
        new Document([
            'document_base64' => base64_encode(file_get_contents('pdf/sample.pdf')),
            'name' => 'sample.pdf',
            'document_id' => '1',
        ]),
    ]);
  
    // Create a signer
    $signer = new Signer([
        'email' => 'vaishnavipahari612@gmail.com',
        'name' => 'vaishnavi pahari',
        'recipient_id' => '1',
    ]);
    
    // Create a SignHere tab
    $signHereTab = new SignHere([
        'document_id' => '1',
        'page_number' => '1',
        'recipient_id' => '1',
        'x_position' => '100',
        'y_position' => '100',
    ]);
     
    // Create a Tabs object
    $tabs = new Tabs();
    $tabs->setSignHereTabs([$signHereTab]);
    
    // Set the signer's tabs
    $signer->setTabs($tabs);
  
    // Add the signer to the envelope definition
    $recipients = new Recipients();
    $recipients->setSigners([$signer]);
    $envelopeDefinition->setRecipients($recipients);
     
    // Create and send the envelope
    $envelopesApi = new EnvelopesApi($apiClient);
 
    $envelopeSummary = $envelopesApi->createEnvelope("fc211775-c990-4ac0-bc2c-0d28fd15a15d", $envelopeDefinition);
       print_r($envelopeSummary);
  die;
    echo "hello";
    print_r($envelopeSummary);
 
} else {
    // Handle the error
    echo 'Error: Access token not found in response.';
}

?>
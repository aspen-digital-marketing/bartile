<?php

require 'vendor/autoload.php';


$clientId = 'b2ea1aaf-ba96-4d5d-8d3f-76e633a852ce';
$clientSecret = 'b7ceaea6-5b42-4f64-982c-5485482b5e3f';
$redirectUri = 'https://bartile.goaspendigital.com/docsign/redirect.php';

// Create a new OAuth 2.0 client
$provider = new League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => $clientId,
    'clientSecret'            => $clientSecret,
    'redirectUri'             => $redirectUri,
    'urlAuthorize'            => 'https://account-d.docusign.com/oauth/auth',
    'urlAccessToken'          => 'https://account-d.docusign.com/oauth/token',
    'urlResourceOwnerDetails' => '',
]);

// Check if we have an authorization code
if (!isset($_GET['code'])) {
    // Redirect the user to DocuSign for authorization
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authUrl);
    exit;
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    // Check for CSRF (Cross-Site Request Forgery) attack
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
} else {
    // Try to get an access token using the authorization code
    try {
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code'],
        ]);

        // Save the access token securely for later use in API requests
        $accessTokenString = $accessToken->getToken();
        // Store $accessTokenString in a secure location, such as a database or environment variable
        // ...

        echo 'Access token: ' . $accessTokenString;
    } catch (Exception $e) {
        // Handle any errors that occurred during the token request
        echo 'Error: ' . $e->getMessage();
    }
}



?>
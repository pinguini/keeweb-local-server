<?php
require_once('../../vendor/autoload.php');
require_once(__DIR__ . '/../../config.php');

if (isset($_GET['return'])){
    $_SESSION['oauth_redirect']=$_GET['return'];
}

$provider = new \Omines\OAuth2\Client\Provider\Gitlab([
    'clientId'          => OAUTH_CLIENT,
    'clientSecret'      => OAUTH_SECRET,
    'redirectUri'       => $_SESSION['oauth_redirect'].'auth/gitlab.php',
    'domain'            => OAUTH_SERVER
]);

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();

    $_SESSION['oauth2state'] = $provider->getState();

    header('Location: '.$authUrl);

    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    unset($_SESSION['username']);
    exit('Invalid oauth state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        //printf('Hello %s!', $user->getName());
        $_SESSION['username'] = $user->getName();
        $_SESSION['oauth_gitlab'] = true;
        header('Location: '.$_SESSION['oauth_redirect']);

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    //echo $token->getToken();
}
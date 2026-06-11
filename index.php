<?php
require_once __DIR__ . '/vendor/autoload.php';

use Jumbojett\OpenIDConnectClient;

$config = require __DIR__ . '/config.php';

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => true,     // Required for HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Simple Router logic to handle /signin-oidc
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

try {
    $oidc = new OpenIDConnectClient(
        $config['issuer'],
        $config['client_id'],
        $config['client_secret']
    );

    $oidc->setRedirectURL($config['redirect_uri']);
    
    // Enable PKCE for enhanced security (Required by this IdentityHub)
    $oidc->setCodeChallengeMethod('S256');

    foreach ($config['scopes'] as $scope) {
        $oidc->addScope($scope);
    }

    // Force OIDC provider to show the login screen (bypass auto-login)
    $oidc->addAuthParam(['prompt' => 'login']);

    // If we are at the redirect URI or if we are just starting the login
    // openid-connect-php handles the state check automatically
    $oidc->authenticate();

    // After successful authentication
    $_SESSION['user_info'] = (array) $oidc->getVerifiedClaims();
    $_SESSION['id_token'] = $oidc->getIdToken();
    $_SESSION['access_token'] = $oidc->getAccessToken();

    header("Location: welcome.php");
    exit();

} catch (Exception $e) {
    echo "<h1>Authentication Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}

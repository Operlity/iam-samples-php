<?php
session_start();

$config = require __DIR__ . '/config.php';

// Clear local session
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirect to IdentityHub endsession endpoint
// This ensures the user is also logged out from the identity provider
$logoutUrl = $config['end_session_endpoint'];

header("Location: " . $logoutUrl);
exit();

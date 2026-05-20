<?php
/**
 * PHP Built-in Server Router
 */

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if ($path === '/signin-oidc') {
    include __DIR__ . '/index.php';
    exit;
}

// If it's a file that exists, serve it
if (file_exists(__DIR__ . $path) && !is_dir(__DIR__ . $path)) {
    return false;
}

// Default to index.php
include __DIR__ . '/index.php';

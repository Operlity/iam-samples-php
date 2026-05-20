<?php
/**
 * IdentityHub Configuration
 */

return [
    'issuer' => 'https://id.demo.operlity.com',
    'client_id' => 'ee772e0e-4f95-48a5-8bbb-f2adb0696109',
    'client_secret' => 'fU3OTIb4zT0bzyItYvq6gELBF909uiKASDJBHL1M6c8=',
    'redirect_uri' => 'https://localhost:4500/signin-oidc',
    'scopes' => ['openid', 'profile', 'email'],
    'end_session_endpoint' => 'https://id.demo.operlity.com/connect/endsession'
];

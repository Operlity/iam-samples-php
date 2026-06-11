<?php
/**
 * IdentityHub Configuration
 */

return [
    'issuer' => 'https://ogsiamapp.azurewebsites.net',
    'client_id' => '33e1f48d-8071-4f3a-b8b5-3d0948f9a93d',
    'client_secret' => 'ICALeUurpuzYfGxh38VJLGeLlC5NtYgCyT+l4/nudfY=',
    'redirect_uri' => 'https://localhost:7284/signin-oidc',
    'scopes' => ['openid', 'profile', 'email'],
    'end_session_endpoint' => 'https://ogsiamapp.azurewebsites.net/connect/endsession'
];

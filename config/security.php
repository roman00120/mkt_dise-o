<?php

return [
    'headers' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'Referrer-Policy' => env('SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        'Permissions-Policy' => env('SECURITY_PERMISSIONS_POLICY', 'camera=(), microphone=(), geolocation=()'),
        'Cross-Origin-Opener-Policy' => env('SECURITY_COOP', 'same-origin-allow-popups'),
        'Cross-Origin-Resource-Policy' => env('SECURITY_CORP', 'same-origin'),
    ],
    'content_security_policy' => env('SECURITY_CSP', "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'; img-src 'self' data: blob:; font-src 'self' data:; connect-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'"),
];

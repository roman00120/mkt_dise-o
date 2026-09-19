<?php

return [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-flash-latest'),
    'timeout' => (int) env('GEMINI_TIMEOUT', 30),
    'ca_bundle' => env('GEMINI_CA_BUNDLE'),
];

<?php

// Add to your config/services.php:
return [
    // ... other services

    'veilmail' => [
        'api_key' => env('VEILMAIL_API_KEY'),
        'from_email' => env('VEILMAIL_FROM_EMAIL', 'noreply@veilmail.xyz'),
    ],
];

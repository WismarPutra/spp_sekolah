<?php

return [
    'client_id' => env('DOKU_CLIENT_ID'),
    'secret_key' => env('DOKU_SECRET_KEY', env('DOKU_SHARED_KEY')),
    'is_production' => env('DOKU_IS_PRODUCTION', env('DOKU_ENVIRONMENT') === 'production'),
];

<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Lexware Office API Key
    |--------------------------------------------------------------------------
    |
    | This is your Lexware Office API key which you can find in your Lexware
    | Office account settings. This key is required for all API operations.
    |
    */
    'api_key' => env('LEXOFFICE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Lexware Office API. You shouldn't need to change
    | this unless you're using a different API endpoint for testing.
    |
    */
    'base_url' => env('LEXOFFICE_BASE_URL', 'https://api.lexoffice.io/v1'),
];

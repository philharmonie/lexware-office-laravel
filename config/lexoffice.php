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
    'base_url' => env('LEXOFFICE_BASE_URL', 'https://api.lexware.io/v1'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for API requests. Default is 30 seconds.
    |
    */
    'timeout' => env('LEXOFFICE_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Attempts
    |--------------------------------------------------------------------------
    |
    | The number of retry attempts for failed requests. Default is 3.
    | Only 5xx errors and 429 (rate limit) will be retried.
    |
    */
    'retry_attempts' => env('LEXOFFICE_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | The cache time-to-live in seconds for API responses. Default is 300 seconds (5 minutes).
    | Set to 0 to disable caching.
    |
    */
    'cache_ttl' => env('LEXOFFICE_CACHE_TTL', 300),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Enable or disable automatic rate limiting. When enabled, the client will
    | automatically throttle requests to stay within the 2 requests per second limit.
    |
    */
    'rate_limiting_enabled' => env('LEXOFFICE_RATE_LIMITING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable or disable detailed logging of API requests and responses.
    | This is useful for debugging but should be disabled in production.
    |
    */
    'logging_enabled' => env('LEXOFFICE_LOGGING_ENABLED', false),
];

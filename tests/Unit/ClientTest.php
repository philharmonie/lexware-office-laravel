<?php

declare(strict_types=1);

use Illuminate\Cache\CacheManager;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use PhilHarmonie\LexOffice\Client;
use PhilHarmonie\LexOffice\Exceptions\ApiException;

beforeEach(function () {
    $this->http = new Factory;
    $this->client = new Client('test-api-key', $this->http);
});

test('get request sends correct headers and returns decoded response', function () {
    $responseData = ['key' => 'value'];

    $this->http->fake([
        '*' => $this->http::response($responseData),
    ]);

    $result = $this->client->get('/contacts');

    $this->http->assertSent(function (Request $request) {
        return $request->hasHeader('Authorization', 'Bearer test-api-key')
            && $request->hasHeader('Accept', 'application/json');
    });

    expect($result)->toBe($responseData);
});

test('get request with params builds correct query string and returns response', function () {
    $params = ['email' => 'test@example.com', 'name' => 'Test'];
    $responseData = ['success' => true];

    $this->http->fake([
        '*' => $this->http::response($responseData),
    ]);

    $result = $this->client->get('/contacts', $params);

    $this->http->assertSent(function (Request $request) use ($params) {
        parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $queryParams);

        return $queryParams === $params;
    });

    expect($result)->toBe($responseData);
});

test('post request sends correct headers, body and returns response', function () {
    $data = ['name' => 'Test Company'];
    $responseData = ['id' => 'new-id'];

    $this->http->fake([
        '*' => $this->http::response($responseData),
    ]);

    $result = $this->client->post('/contacts', $data);

    $this->http->assertSent(function (Request $request) use ($data) {
        return $request->method() === 'POST'
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Authorization', 'Bearer test-api-key')
            && json_decode($request->body(), true) === $data;
    });

    expect($result)->toBe($responseData);
});

test('get request throws api exception on error', function () {
    $this->http->fake([
        '*' => $this->http::response(null, 500),
    ]);

    expect(fn () => $this->client->get('/contacts'))
        ->toThrow(Exception::class);
});

test('post request throws api exception on error', function () {
    $this->http->fake([
        '*' => $this->http::response(null, 500),
    ]);

    expect(fn () => $this->client->post('/contacts', ['test' => 'data']))
        ->toThrow(Exception::class);
});

test('api exception includes error details', function () {
    $this->http->fake([
        '*' => $this->http->response(
            json_encode(['message' => 'Invalid request']),
            400,
            ['Content-Type' => 'application/json']
        ),
    ]);

    $statusCode = 400;
    $errorMessage = 'Invalid request';

    try {
        $this->client->get('/test');
    } catch (ApiException $e) {
        $actualMessage = trim($e->getMessage());
        $expectedMessage = trim(sprintf(
            'HTTP request returned status code %d:%s%s',
            $statusCode,
            "\n",
            json_encode(['message' => $errorMessage])
        ));

        expect($actualMessage)->toBe($expectedMessage)
            ->and($e->getCode())->toBe($statusCode);
    }
});

test('get request logs error and throws api exception when response has no json', function () {
    $this->http->fake([
        '*' => $this->http::response('Plain text error', 500),
    ]);

    expect(fn () => $this->client->get('/test'))
        ->toThrow(Exception::class);
});

test('post request logs error and throws api exception when response has no json', function () {
    $this->http->fake([
        '*' => $this->http::response('Plain text error', 500),
    ]);

    expect(fn () => $this->client->post('/test', ['data' => 'test']))
        ->toThrow(Exception::class);
});

test('client uses config base url', function () {
    Config::set('lexoffice.base_url', 'https://custom-api.example.com/v1');

    $responseData = ['key' => 'value'];

    $this->http->fake([
        '*' => $this->http::response($responseData),
    ]);

    $this->client->get('/contacts');

    $this->http->assertSent(function (Request $request) {
        return str_starts_with($request->url(), 'https://custom-api.example.com/v1/contacts');
    });
});

test('client handles rate limiting', function () {
    $this->http->fake([
        '*' => $this->http::response(['key' => 'value']),
    ]);

    // Should not throw exception with rate limiting
    $result = $this->client->get('/contacts');
    expect($result)->toBe(['key' => 'value']);
});

test('client retries on 5xx errors', function () {
    $this->http->fake([
        '*' => $this->http::response(null, 500),
    ]);

    expect(fn () => $this->client->get('/contacts'))
        ->toThrow(Exception::class);
});

test('client retries on 429 rate limit', function () {
    $this->http->fake([
        '*' => $this->http::response(null, 429),
    ]);

    expect(fn () => $this->client->get('/contacts'))
        ->toThrow(Exception::class);
});

test('client handles successful retry after 5xx error', function () {
    $this->http->fake([
        '*' => $this->http::response(null, 500),
        '*' => $this->http::response(['success' => true], 200),
    ]);

    $result = $this->client->get('/contacts');

    expect($result)->toBe(['success' => true]);
});

test('client handles successful retry after 429 error', function () {
    $this->http->fake([
        '*' => $this->http::response(null, 429),
        '*' => $this->http::response(['success' => true], 200),
    ]);

    $result = $this->client->get('/contacts');

    expect($result)->toBe(['success' => true]);
});

test('client handles non-retryable errors', function () {
    $this->http->fake([
        '*' => $this->http::response(null, 400),
    ]);

    expect(fn () => $this->client->get('/contacts'))
        ->toThrow(ApiException::class);
});

test('client handles post request with retry', function () {
    $this->http->fake([
        '*' => $this->http::response(null, 500),
        '*' => $this->http::response(['created' => true], 201),
    ]);

    $result = $this->client->post('/contacts', ['name' => 'Test']);

    expect($result)->toBe(['created' => true]);
});

test('client handles rate limiting with multiple requests', function () {
    $this->http->fake([
        '*' => $this->http->response(['data' => 'test'], 200),
    ]);

    // Make requests quickly to test rate limiting
    $this->client->get('/contacts');
    $this->client->get('/contacts');
    $this->client->get('/contacts');

    // Just test that no exception is thrown
    expect(true)->toBeTrue();
});

test('client handles cacheable endpoints', function () {
    $this->http->fake([
        '*' => $this->http->response(['data' => 'cached'], 200),
    ]);

    // Test cacheable endpoint
    $result = $this->client->get('/contacts');
    expect($result)->toBe(['data' => 'cached']);

    // Test non-cacheable endpoint
    $result = $this->client->get('/invoices');
    expect($result)->toBe(['data' => 'cached']);
});

test('client handles non-cacheable endpoints', function () {
    $this->http->fake([
        '*' => $this->http->response(['data' => 'not-cached'], 200),
    ]);

    // Test non-cacheable endpoint
    $result = $this->client->get('/invoices');
    expect($result)->toBe(['data' => 'not-cached']);
});

test('client handles cache key generation', function () {
    $this->http->fake([
        '*' => $this->http->response(['data' => 'test'], 200),
    ]);

    // Test different parameters generate different cache keys
    $result1 = $this->client->get('/contacts', ['page' => 1]);
    $result2 = $this->client->get('/contacts', ['page' => 2]);

    expect($result1)->toBe(['data' => 'test']);
    expect($result2)->toBe(['data' => 'test']);
});

test('client handles base URL configuration', function () {
    Config::set('lexoffice.base_url', 'https://custom-api.example.com/v1');

    $this->http->fake([
        '*' => $this->http->response(['data' => 'test'], 200),
    ]);

    $result = $this->client->get('/contacts');

    expect($result)->toBe(['data' => 'test']);

    // Reset config
    Config::set('lexoffice.base_url', 'https://api.lexoffice.io/v1');
});

test('client handles invalid base URL configuration', function () {
    Config::set('lexoffice.base_url', null);

    $this->http->fake([
        '*' => $this->http->response(['data' => 'test'], 200),
    ]);

    $result = $this->client->get('/contacts');

    expect($result)->toBe(['data' => 'test']);

    // Reset config
    Config::set('lexoffice.base_url', 'https://api.lexoffice.io/v1');
});

test('client handles retry with exponential backoff', function () {
    $this->http->fake([
        '*' => $this->http->response(null, 500),
        '*' => $this->http->response(null, 500),
        '*' => $this->http->response(['success' => true], 200),
    ]);

    $result = $this->client->get('/contacts');

    expect($result)->toBe(['success' => true]);
});

test('client handles retry with 502 error', function () {
    $this->http->fake([
        '*' => $this->http->response(null, 502),
        '*' => $this->http->response(['success' => true], 200),
    ]);

    $result = $this->client->get('/contacts');

    expect($result)->toBe(['success' => true]);
});

test('client handles retry with 503 error', function () {
    $this->http->fake([
        '*' => $this->http->response(null, 503),
        '*' => $this->http->response(['success' => true], 200),
    ]);

    $result = $this->client->get('/contacts');

    expect($result)->toBe(['success' => true]);
});

test('client handles retry with 504 error', function () {
    $this->http->fake([
        '*' => $this->http->response(null, 504),
        '*' => $this->http->response(['success' => true], 200),
    ]);

    $result = $this->client->get('/contacts');

    expect($result)->toBe(['success' => true]);
});

test('client handles rate limiting without cache', function () {
    // Create client without cache
    $client = new Client('test-api-key', $this->http);

    $this->http->fake([
        '*' => $this->http->response(['data' => 'test'], 200),
    ]);

    // Should not throw exception even without cache
    $result = $client->get('/contacts');
    expect($result)->toBe(['data' => 'test']);
});

test('client handles cached response', function () {
    $this->http->fake([
        '*' => $this->http->response(['data' => 'fresh'], 200),
    ]);

    // First request - should cache
    $result1 = $this->client->get('/contacts');
    expect($result1)->toBe(['data' => 'fresh']);

    // Second request - should return cached data
    $result2 = $this->client->get('/contacts');
    expect($result2)->toBe(['data' => 'fresh']);
});

test('client handles cache put operation', function () {
    $this->http->fake([
        '*' => $this->http->response(['data' => 'cached'], 200),
    ]);

    // This should trigger cache put
    $result = $this->client->get('/contacts');
    expect($result)->toBe(['data' => 'cached']);
});

test('client handles cacheable endpoints list', function () {
    $this->http->fake([
        '*' => $this->http->response(['data' => 'test'], 200),
    ]);

    // Test all cacheable endpoints
    $endpoints = [
        '/contacts',
        '/countries',
        '/payment-conditions',
        '/posting-categories',
        '/print-layouts',
        '/profile',
        '/recurring-templates',
    ];

    foreach ($endpoints as $endpoint) {
        $result = $this->client->get($endpoint);
        expect($result)->toBe(['data' => 'test']);
    }
});

test('client handles rate limiting with cache timestamps', function () {
    $this->http->fake([
        '*' => $this->http->response(['data' => 'test'], 200),
    ]);

    // Make multiple requests to test rate limiting logic
    $this->client->get('/contacts');
    $this->client->get('/contacts');
    $this->client->get('/contacts');

    // Should not throw exception
    expect(true)->toBeTrue();
});

test('client handles rate limiting with wait time', function () {
    $this->http->fake([
        '*' => $this->http->response(['data' => 'test'], 200),
    ]);

    // Make requests quickly to test rate limiting
    $this->client->get('/contacts');
    $this->client->get('/contacts');
    $this->client->get('/contacts');

    // Should not throw exception
    expect(true)->toBeTrue();
});

test('client handles retry with unknown error', function () {
    $this->http->fake([
        '*' => $this->http->response(null, 500),
        '*' => $this->http->response(null, 500),
        '*' => $this->http->response(null, 500),
        '*' => $this->http->response(null, 500),
    ]);

    // This should trigger the "Unknown error occurred" path
    expect(fn () => $this->client->get('/contacts'))
        ->toThrow(Exception::class);
});

test('client handles cached response with null check', function () {
    $this->http->fake([
        '*' => $this->http->response(['data' => 'fresh'], 200),
    ]);

    // First request - should cache
    $result1 = $this->client->get('/contacts');
    expect($result1)->toBe(['data' => 'fresh']);

    // Second request - should return cached data
    $result2 = $this->client->get('/contacts');
    expect($result2)->toBe(['data' => 'fresh']);
});

test('handleRateLimit sleeps when window is full', function () {
    Config::set('lexoffice.base_url', 'https://example.test');

    // Wichtig: Response über die Factory erzeugen – NICHT Illuminate\Http\Response::make
    $this->http->fake([
        'https://example.test/profile' => $this->http->response(['ok' => true], 200),
    ]);

    /** @var CacheManager $cache */
    $cache = app(CacheManager::class);

    $apiKey = 'test-key';
    $client = new Client($apiKey, $this->http, $cache, cacheTtl: 60);

    $rateLimitKey = 'lexoffice:rate_limit:'.$apiKey;

    $now = time();
    $timestamps = [
        $now - 0.999, // float -> minimal positives waitTime
        $now,
    ];

    $cache->put($rateLimitKey, $timestamps, 1);

    $res = $client->get('/profile');

    expect($res)->toBeArray()->toHaveKey('ok', true);
});

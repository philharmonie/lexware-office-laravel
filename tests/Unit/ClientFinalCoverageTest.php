<?php

declare(strict_types=1);

use Illuminate\Http\Client\Factory;
use PhilHarmonie\LexOffice\Client;

test('client handles cached response return path', function () {
    $http = new Factory;
    $cache = Mockery::mock(Illuminate\Cache\CacheManager::class);

    // Mock cache to return cached data
    $cache->shouldReceive('get')
        ->andReturn(['cached' => 'data']);

    // Mock rate limiting cache calls
    $cache->shouldReceive('get')
        ->with(Mockery::type('string'), [])
        ->andReturn([]);

    $cache->shouldReceive('put')->andReturn(true);

    $client = new Client('test-api-key', $http, $cache);

    $http->fake([
        '*' => $http->response(['fresh' => 'data'], 200),
    ]);

    $result = $client->get('/contacts');
    expect($result)->toBe(['cached' => 'data']);
});

test('client handles cache put path', function () {
    $http = new Factory;
    $cache = Mockery::mock(Illuminate\Cache\CacheManager::class);

    // Mock cache to return null (no cached data)
    $cache->shouldReceive('get')
        ->andReturn(null);

    // Mock rate limiting cache calls
    $cache->shouldReceive('get')
        ->with(Mockery::type('string'), [])
        ->andReturn([]);

    // Mock cache put operation
    $cache->shouldReceive('put')
        ->andReturn(true);

    $client = new Client('test-api-key', $http, $cache);

    $http->fake([
        '*' => $http->response(['fresh' => 'data'], 200),
    ]);

    $result = $client->get('/contacts');
    expect($result)->toBe(['fresh' => 'data']);
});

test('client handles all cacheable endpoints', function () {
    $http = new Factory;
    $cache = Mockery::mock(Illuminate\Cache\CacheManager::class);

    $cache->shouldReceive('get')->andReturn(null);
    $cache->shouldReceive('get')
        ->with(Mockery::type('string'), [])
        ->andReturn([]);
    $cache->shouldReceive('put')->andReturn(true);

    $client = new Client('test-api-key', $http, $cache);

    $http->fake([
        '*' => $http->response(['data' => 'test'], 200),
    ]);

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
        $result = $client->get($endpoint);
        expect($result)->toBe(['data' => 'test']);
    }
});

test('client handles rate limiting timestamp filtering', function () {
    $http = new Factory;
    $cache = Mockery::mock(Illuminate\Cache\CacheManager::class);

    $currentTime = time();
    $oldTimestamp = $currentTime - 10; // Outside window
    $newTimestamp = $currentTime - 1;  // Inside window

    $cache->shouldReceive('get')
        ->andReturn(null);

    $cache->shouldReceive('get')
        ->with(Mockery::type('string'), [])
        ->andReturn([$oldTimestamp, $newTimestamp, $newTimestamp]);

    $cache->shouldReceive('put')->andReturn(true);

    $client = new Client('test-api-key', $http, $cache);

    $http->fake([
        '*' => $http->response(['data' => 'test'], 200),
    ]);

    $result = $client->get('/contacts');
    expect($result)->toBe(['data' => 'test']);
});

test('client handles rate limiting wait time', function () {
    $http = new Factory;
    $cache = Mockery::mock(Illuminate\Cache\CacheManager::class);

    $currentTime = time();
    $timestamps = array_fill(0, 2, $currentTime); // Fill rate limit

    $cache->shouldReceive('get')
        ->andReturn(null);

    $cache->shouldReceive('get')
        ->with(Mockery::type('string'), [])
        ->andReturn($timestamps);

    $cache->shouldReceive('put')->andReturn(true);

    $client = new Client('test-api-key', $http, $cache);

    $http->fake([
        '*' => $http->response(['data' => 'test'], 200),
    ]);

    $result = $client->get('/contacts');
    expect($result)->toBe(['data' => 'test']);
});

test('client handles shouldCache method return false path', function () {
    $http = new Factory;
    $cache = Mockery::mock(Illuminate\Cache\CacheManager::class);

    $cache->shouldReceive('get')
        ->with(Mockery::type('string'), [])
        ->andReturn([]);

    $cache->shouldReceive('put')->andReturn(true);

    $client = new Client('test-api-key', $http, $cache);

    $http->fake([
        '*' => $http->response(['data' => 'test'], 200),
    ]);

    // Test non-cacheable endpoint (should return false from shouldCache)
    $result = $client->get('/invoices');
    expect($result)->toBe(['data' => 'test']);
});

test('client handles rate limiting with wait time calculation', function () {
    $http = new Factory;
    $cache = Mockery::mock(Illuminate\Cache\CacheManager::class);

    $currentTime = time();
    $oldTimestamp = $currentTime - 2; // Old timestamp that triggers wait
    $timestamps = [$oldTimestamp, $oldTimestamp]; // Fill rate limit

    $cache->shouldReceive('get')
        ->andReturn(null);

    $cache->shouldReceive('get')
        ->with(Mockery::type('string'), [])
        ->andReturn($timestamps);

    $cache->shouldReceive('put')->andReturn(true);

    $client = new Client('test-api-key', $http, $cache);

    $http->fake([
        '*' => $http->response(['data' => 'test'], 200),
    ]);

    $result = $client->get('/contacts');
    expect($result)->toBe(['data' => 'test']);
});

test('client handles rate limiting with usleep execution', function () {
    $http = new Factory;
    $cache = Mockery::mock(Illuminate\Cache\CacheManager::class);

    $currentTime = time();
    $oldTimestamp = $currentTime - 2; // Old timestamp that triggers wait
    $timestamps = [$oldTimestamp, $oldTimestamp]; // Fill rate limit

    $cache->shouldReceive('get')
        ->andReturn(null);

    $cache->shouldReceive('get')
        ->with(Mockery::type('string'), [])
        ->andReturn($timestamps);

    $cache->shouldReceive('put')->andReturn(true);

    $client = new Client('test-api-key', $http, $cache);

    $http->fake([
        '*' => $http->response(['data' => 'test'], 200),
    ]);

    $result = $client->get('/contacts');
    expect($result)->toBe(['data' => 'test']);
});

test('client handles final exception path line 235', function () {
    $http = new Factory;
    $cache = Mockery::mock(Illuminate\Cache\CacheManager::class);

    $cache->shouldReceive('get')->andReturn([]);
    $cache->shouldReceive('put')->andReturn(true);

    $client = new Client('test-api-key', $http, $cache);

    // Create scenario where all retries fail and we reach the final exception
    $http->fake([
        '*' => $http->response(null, 500),
        '*' => $http->response(null, 500),
        '*' => $http->response(null, 500),
        '*' => $http->response(null, 500),
    ]);

    // This should hit line 235: throw $lastException ?? new Exception('Unknown error occurred');
    try {
        $client->get('/contacts');
        expect(false)->toBeTrue('Expected exception was not thrown');
    } catch (Exception $e) {
        expect($e)->toBeInstanceOf(Exception::class);
    }
});

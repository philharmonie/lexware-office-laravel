<?php

declare(strict_types=1);

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
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
        ->toThrow(ApiException::class);
});

test('post request throws api exception on error', function () {
    $this->http->fake([
        '*' => $this->http::response(null, 500),
    ]);

    expect(fn () => $this->client->post('/contacts', ['test' => 'data']))
        ->toThrow(ApiException::class);
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

<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Contracts\ClientInterface;
use PhilHarmonie\LexOffice\Services\ContactService;

beforeEach(function () {
    $this->client = Mockery::mock(ClientInterface::class);
    $this->service = new ContactService($this->client);
});

test('find method calls client with correct endpoint', function () {
    $id = 'test-id';
    $expectedResponse = ['id' => $id];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with("/contacts/{$id}")
        ->andReturn($expectedResponse);

    $result = $this->service->find($id);
    expect($result)->toBe($expectedResponse);
});

test('list method calls client with correct parameters', function () {
    $filters = ['email' => 'test@example.com'];
    $expectedResponse = ['contacts' => []];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with('/contacts', $filters)
        ->andReturn($expectedResponse);

    $result = $this->service->list($filters);
    expect($result)->toBe($expectedResponse);
});

test('withoutCache method returns new service instance', function () {
    $originalService = $this->service;

    $this->client
        ->shouldReceive('withoutCache')
        ->once()
        ->andReturnSelf();

    $withoutCacheService = $this->service->withoutCache();

    expect($withoutCacheService)->not->toBe($originalService)
        ->and($withoutCacheService)->toBeInstanceOf(ContactService::class);
});

test('withoutCache service bypasses cache for requests', function () {
    $expectedResponse = ['contacts' => []];

    $this->client
        ->shouldReceive('withoutCache')
        ->once()
        ->andReturnSelf();

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with('/contacts', [])
        ->andReturn($expectedResponse);

    $withoutCacheService = $this->service->withoutCache();
    $result = $withoutCacheService->list();

    expect($result)->toBe($expectedResponse);
});

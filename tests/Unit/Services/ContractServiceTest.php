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

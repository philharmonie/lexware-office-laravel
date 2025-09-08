<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Contracts\ClientInterface;
use PhilHarmonie\LexOffice\Services\DunningService;

beforeEach(function () {
    $this->client = Mockery::mock(ClientInterface::class);
    $this->service = new DunningService($this->client);
});

test('create method calls client with correct endpoint', function () {
    $data = ['some' => 'data'];
    $expectedResponse = ['id' => 'new-dunning-id'];

    $this->client
        ->expects('post')
        ->with('/dunnings', $data)
        ->andReturn($expectedResponse);

    $result = $this->service->create($data);

    expect($result)->toBe($expectedResponse);
});

test('find method calls client with correct endpoint', function () {
    $id = 'test-dunning-id';
    $expectedResponse = ['id' => $id];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with("/dunnings/{$id}")
        ->andReturn($expectedResponse);

    $result = $this->service->find($id);
    expect($result)->toBe($expectedResponse);
});

test('pursue method calls client with correct endpoint', function () {
    $id = 'test-dunning-id';
    $expectedResponse = ['id' => $id, 'status' => 'pursued'];

    $this->client
        ->expects('post')
        ->with("/dunnings/{$id}/pursue")
        ->andReturn($expectedResponse);

    $result = $this->service->pursue($id);
    expect($result)->toBe($expectedResponse);
});

test('render method calls client with correct endpoint', function () {
    $id = 'test-dunning-id';
    $expectedResponse = ['documentFileId' => 'file-id'];

    $this->client
        ->expects('post')
        ->with("/dunnings/{$id}/document")
        ->andReturn($expectedResponse);

    $result = $this->service->render($id);
    expect($result)->toBe($expectedResponse);
});

test('download method calls client with correct endpoint', function () {
    $id = 'test-dunning-id';
    $expectedResponse = ['binary' => 'base64-encoded-pdf'];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with("/dunnings/{$id}/document")
        ->andReturn($expectedResponse);

    $result = $this->service->download($id);
    expect($result)->toBe($expectedResponse);
});

test('deeplink method calls client with correct endpoint', function () {
    $id = 'test-dunning-id';
    $expectedResponse = ['deeplink' => 'https://app.lexware.de/dunning/123'];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with("/dunnings/{$id}/deeplink")
        ->andReturn($expectedResponse);

    $result = $this->service->deeplink($id);
    expect($result)->toBe($expectedResponse);
});

<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Contracts\ClientInterface as ClientInterfaceAlias;
use PhilHarmonie\LexOffice\Services\InvoiceService;

beforeEach(function () {
    $this->client = Mockery::mock(ClientInterfaceAlias::class);
    $this->service = new InvoiceService($this->client);
});

test('create method calls client with correct endpoint', function () {
    $data = ['some' => 'data'];
    $expectedResponse = ['id' => 'new-id'];

    $this->client
        ->expects('post')
        ->with('/invoices', $data)
        ->andReturn($expectedResponse);

    $result = $this->service->create($data);

    expect($result)->toBe($expectedResponse);
});

test('create method adds finalize parameter when true', function () {
    $data = ['some' => 'data'];

    $this->client
        ->expects('post')
        ->with('/invoices?finalize=true', $data)
        ->andReturn([]);

    $this->service->create($data, true);
});

test('find method calls client with correct endpoint', function () {
    $id = 'test-id';
    $expectedResponse = ['id' => $id];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with("/invoices/{$id}")
        ->andReturn($expectedResponse);

    $result = $this->service->find($id);
    expect($result)->toBe($expectedResponse);
});

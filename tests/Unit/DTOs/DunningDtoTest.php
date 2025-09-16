<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\DTOs\DunningDto;
use Tests\Factories\LexOfficeResponseFactory;

test('dunning dto creates from array', function () {
    $data = LexOfficeResponseFactory::dunning();

    $dto = DunningDto::fromArray($data);

    expect($dto->id)->toBe('dunning-123')
        ->and($dto->resourceUri)->toBe('/dunnings/dunning-123')
        ->and($dto->voucherNumber)->toBe('MAHN-001')
        ->and($dto->voucherDate)->toBe('2024-01-01')
        ->and($dto->address)->toBeArray()
        ->and($dto->lineItems)->toBeArray()
        ->and($dto->totalPrice)->toBeArray()
        ->and($dto->taxAmount)->toBeArray()
        ->and($dto->taxType)->toBe('net')
        ->and($dto->paymentConditions)->toBeArray()
        ->and($dto->shippingConditions)->toBeArray()
        ->and($dto->title)->toBe('Payment Reminder')
        ->and($dto->introduction)->toBe('Please pay your outstanding invoice')
        ->and($dto->remark)->toBe('Payment overdue')
        ->and($dto->files)->toBeArray()
        ->and($dto->createdDate)->toBe('2024-01-01T12:00:00.000+01:00')
        ->and($dto->updatedDate)->toBe('2024-01-01T12:00:00.000+01:00')
        ->and($dto->version)->toBe(1);
});

test('dunning dto handles optional fields', function () {
    $data = [
        'id' => 'dunning-456',
        'resourceUri' => '/dunnings/dunning-456',
        'voucherNumber' => 'MAHN-002',
        'voucherDate' => '2024-01-02',
        'address' => null,
        'lineItems' => null,
        'totalPrice' => null,
        'taxAmount' => null,
        'taxType' => null,
        'paymentConditions' => null,
        'shippingConditions' => null,
        'title' => null,
        'introduction' => null,
        'remark' => null,
        'files' => null,
        'createdDate' => '2024-01-02T12:00:00.000+01:00',
        'updatedDate' => '2024-01-02T12:00:00.000+01:00',
        'version' => null,
    ];

    $dto = DunningDto::fromArray($data);

    expect($dto->id)->toBe('dunning-456')
        ->and($dto->address)->toBeNull()
        ->and($dto->lineItems)->toBeNull()
        ->and($dto->totalPrice)->toBeNull()
        ->and($dto->taxAmount)->toBeNull()
        ->and($dto->taxType)->toBeNull()
        ->and($dto->paymentConditions)->toBeNull()
        ->and($dto->shippingConditions)->toBeNull()
        ->and($dto->title)->toBeNull()
        ->and($dto->introduction)->toBeNull()
        ->and($dto->remark)->toBeNull()
        ->and($dto->files)->toBeNull()
        ->and($dto->version)->toBeNull();
});

test('dunning dto converts to array', function () {
    $data = LexOfficeResponseFactory::dunning();
    $dto = DunningDto::fromArray($data);
    $array = $dto->toArray();

    expect($array)->toBe($data);
});

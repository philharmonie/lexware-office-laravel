<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\DTOs\InvoiceDto;
use Tests\Factories\LexOfficeResponseFactory;

test('invoice dto creates from array', function () {
    $data = LexOfficeResponseFactory::invoice();
    $dto = InvoiceDto::fromArray($data);

    expect($dto->id)->toBe('invoice-123')
        ->and($dto->resourceUri)->toBe('https://api.lexware.io/v1/invoices/invoice-123')
        ->and($dto->voucherNumber)->toBe('INV-001')
        ->and($dto->voucherDate)->toBe('2024-01-01T12:00:00.000+01:00')
        ->and($dto->dueDate)->toBe('2024-01-31T12:00:00.000+01:00')
        ->and($dto->address)->toBeArray()
        ->and($dto->lineItems)->toBeArray()
        ->and($dto->totalPrice)->toBeArray()
        ->and($dto->taxAmount)->toBeArray()
        ->and($dto->taxType)->toBe('net')
        ->and($dto->paymentConditions)->toBeArray()
        ->and($dto->shippingConditions)->toBeArray()
        ->and($dto->title)->toBe('Invoice')
        ->and($dto->introduction)->toBe('Thank you for your order')
        ->and($dto->remark)->toBe('Please pay within 30 days')
        ->and($dto->files)->toBeArray()
        ->and($dto->createdDate)->toBe('2024-01-01T12:00:00.000+01:00')
        ->and($dto->updatedDate)->toBe('2024-01-01T12:00:00.000+01:00')
        ->and($dto->version)->toBe(1);
});

test('invoice dto handles optional fields', function () {
    $data = [
        'id' => 'invoice-456',
        'resourceUri' => 'https://api.lexware.io/v1/invoices/invoice-456',
        'voucherNumber' => 'INV-002',
        'voucherDate' => '2024-01-01T12:00:00.000+01:00',
        'createdDate' => '2024-01-01T12:00:00.000+01:00',
        'updatedDate' => '2024-01-01T12:00:00.000+01:00',
    ];

    $dto = InvoiceDto::fromArray($data);

    expect($dto->id)->toBe('invoice-456')
        ->and($dto->dueDate)->toBeNull()
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

test('invoice dto converts to array', function () {
    $data = LexOfficeResponseFactory::invoice();
    $dto = InvoiceDto::fromArray($data);
    $array = $dto->toArray();

    expect($array)->toBe($data);
});

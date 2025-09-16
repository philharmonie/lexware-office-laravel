<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\DTOs\ContactDto;
use Tests\Factories\LexOfficeResponseFactory;

test('contact dto creates from array', function () {
    $data = LexOfficeResponseFactory::contact();
    $dto = ContactDto::fromArray($data);

    expect($dto->id)->toBe('contact-123')
        ->and($dto->name)->toBe('Test Company')
        ->and($dto->email)->toBe('test@example.com')
        ->and($dto->phone)->toBe('+49 123 456789')
        ->and($dto->website)->toBe('https://example.com')
        ->and($dto->note)->toBe('Test contact')
        ->and($dto->addresses)->toBeArray()
        ->and($dto->person)->toBeArray()
        ->and($dto->roles)->toBeArray()
        ->and($dto->archived)->toBeFalse()
        ->and($dto->createdDate)->toBe('2024-01-01T12:00:00.000+01:00')
        ->and($dto->updatedDate)->toBe('2024-01-01T12:00:00.000+01:00')
        ->and($dto->version)->toBe(1);
});

test('contact dto handles optional fields', function () {
    $data = [
        'id' => 'contact-456',
        'name' => 'Minimal Company',
        'createdDate' => '2024-01-01T12:00:00.000+01:00',
        'updatedDate' => '2024-01-01T12:00:00.000+01:00',
    ];
    
    $dto = ContactDto::fromArray($data);

    expect($dto->id)->toBe('contact-456')
        ->and($dto->name)->toBe('Minimal Company')
        ->and($dto->email)->toBeNull()
        ->and($dto->phone)->toBeNull()
        ->and($dto->website)->toBeNull()
        ->and($dto->note)->toBeNull()
        ->and($dto->addresses)->toBe([])
        ->and($dto->person)->toBe([])
        ->and($dto->roles)->toBe([])
        ->and($dto->archived)->toBeNull()
        ->and($dto->version)->toBeNull();
});

test('contact dto converts to array', function () {
    $data = LexOfficeResponseFactory::contact();
    $dto = ContactDto::fromArray($data);
    $array = $dto->toArray();

    expect($array)->toBe($data);
});

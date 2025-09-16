<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\DTOs\CountryDto;

test('country dto creates from array', function () {
    $data = [
        'code' => 'DE',
        'name' => 'Germany',
        'taxClassification' => 'domestic',
    ];

    $dto = CountryDto::fromArray($data);

    expect($dto->code)->toBe('DE')
        ->and($dto->name)->toBe('Germany')
        ->and($dto->taxClassification)->toBe('domestic');
});

test('country dto handles optional fields', function () {
    $data = [
        'code' => 'US',
        'name' => 'United States',
        'taxClassification' => null,
    ];

    $dto = CountryDto::fromArray($data);

    expect($dto->code)->toBe('US')
        ->and($dto->name)->toBe('United States')
        ->and($dto->taxClassification)->toBeNull();
});

test('country dto converts to array', function () {
    $data = [
        'code' => 'FR',
        'name' => 'France',
        'taxClassification' => 'eu',
    ];

    $dto = CountryDto::fromArray($data);
    $array = $dto->toArray();

    expect($array)->toBe($data);
});

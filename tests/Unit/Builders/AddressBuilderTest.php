<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Builders\AddressBuilder;

test('it creates an empty address', function () {
    $data = AddressBuilder::make()->toArray();

    expect($data)->toBeArray()->toBeEmpty();
});

test('it sets all address fields', function () {
    $data = AddressBuilder::make()
        ->name('Test Company')
        ->street('Test Street 123')
        ->city('Test City')
        ->zip('12345')
        ->countryCode('DE')
        ->toArray();

    expect($data)->toBe([
        'name' => 'Test Company',
        'street' => 'Test Street 123',
        'city' => 'Test City',
        'zip' => '12345',
        'countryCode' => 'DE',
    ]);
});

test('it allows setting fields individually', function () {
    $address = AddressBuilder::make();

    $address->name('Test Company');
    expect($address->toArray())->toBe(['name' => 'Test Company']);

    $address->street('Test Street 123');
    expect($address->toArray())->toHaveKey('street', 'Test Street 123');

    $address->city('Test City');
    expect($address->toArray())->toHaveKey('city', 'Test City');

    $address->zip('12345');
    expect($address->toArray())->toHaveKey('zip', '12345');

    $address->countryCode('DE');
    expect($address->toArray())->toHaveKey('countryCode', 'DE');
});

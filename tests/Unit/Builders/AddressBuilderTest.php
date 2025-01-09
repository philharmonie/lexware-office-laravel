<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Builders\AddressBuilder;

test('it creates an empty address', function () {
    $data = AddressBuilder::make()->toArray();

    expect($data)->toBeArray()->toBe([
        'supplement' => '', // Default value for supplement
    ]);
});

test('it sets all address fields', function () {
    $data = AddressBuilder::make()
        ->name('Test Company')
        ->supplement('Building 5')
        ->street('Test Street 123')
        ->city('Test City')
        ->zip('12345')
        ->countryCode('DE')
        ->toArray();

    expect($data)->toHaveKey('name', 'Test Company')
        ->toHaveKey('supplement', 'Building 5')
        ->toHaveKey('street', 'Test Street 123')
        ->toHaveKey('city', 'Test City')
        ->toHaveKey('zip', '12345')
        ->toHaveKey('countryCode', 'DE');
});

test('it allows setting fields individually', function () {
    $address = AddressBuilder::make();

    $address->name('Test Company');
    expect($address->toArray())->toHaveKey('name', 'Test Company')
        ->toHaveKey('supplement', ''); // Default value

    $address->street('Test Street 123');
    expect($address->toArray())->toHaveKey('street', 'Test Street 123');

    $address->city('Test City');
    expect($address->toArray())->toHaveKey('city', 'Test City');

    $address->zip('12345');
    expect($address->toArray())->toHaveKey('zip', '12345');

    $address->countryCode('DE');
    expect($address->toArray())->toHaveKey('countryCode', 'DE');
});

test('it sets supplement field with default value', function () {
    $data = AddressBuilder::make()
        ->name('Test Company')
        ->street('Test Street 123')
        ->city('Test City')
        ->zip('12345')
        ->countryCode('DE')
        ->toArray();

    expect($data)->toHaveKey('name', 'Test Company')
        ->toHaveKey('supplement', '') // Default value
        ->toHaveKey('street', 'Test Street 123')
        ->toHaveKey('city', 'Test City')
        ->toHaveKey('zip', '12345')
        ->toHaveKey('countryCode', 'DE');
});

test('it allows setting supplement field individually', function () {
    $address = AddressBuilder::make();

    $address->supplement('Building 5');
    expect($address->toArray())->toHaveKey('supplement', 'Building 5');
});

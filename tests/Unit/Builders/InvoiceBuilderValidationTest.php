<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Builders\AddressBuilder;
use PhilHarmonie\LexOffice\Builders\InvoiceBuilder;
use PhilHarmonie\LexOffice\Builders\LineItemBuilder;

test('invoice builder validation can handle invalid line item array', function () {
    $builder = InvoiceBuilder::make()
        ->address(AddressBuilder::make()->name('Test Company')->street('Test Street')->city('Test City')->zip('12345')->countryCode('DE'))
        ->addLineItem(LineItemBuilder::custom()
            ->name('Test Product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 99.99, 19.0));

    // Use reflection to modify internal state
    $reflection = new ReflectionClass($builder);
    $dataProperty = $reflection->getProperty('data');
    $dataProperty->setAccessible(true);
    $data = $dataProperty->getValue($builder);

    // Modify line items to be invalid
    $data['lineItems'][0] = 'invalid_string';
    $dataProperty->setValue($builder, $data);

    expect(fn () => $builder->toValidatedArray())
        ->toThrow(InvalidArgumentException::class, 'Line item at index 0 must be an array.');
});

test('invoice builder validation can handle invalid unitPrice', function () {
    $builder = InvoiceBuilder::make()
        ->address(AddressBuilder::make()->name('Test Company')->street('Test Street')->city('Test City')->zip('12345')->countryCode('DE'))
        ->addLineItem(LineItemBuilder::custom()
            ->name('Test Product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 99.99, 19.0));

    // Use reflection to modify internal state
    $reflection = new ReflectionClass($builder);
    $dataProperty = $reflection->getProperty('data');
    $dataProperty->setAccessible(true);
    $data = $dataProperty->getValue($builder);

    // Modify unitPrice to be invalid
    $data['lineItems'][0]['unitPrice'] = 'invalid_string';
    $dataProperty->setValue($builder, $data);

    expect(fn () => $builder->toValidatedArray())
        ->toThrow(InvalidArgumentException::class, 'Line item at index 0 unitPrice must be an array.');
});

test('invoice builder validation handles tax conditions validation', function () {
    $builder = InvoiceBuilder::make()
        ->address(AddressBuilder::make()->name('Test Company')->street('Test Street')->city('Test City')->zip('12345')->countryCode('DE'))
        ->addLineItem(LineItemBuilder::custom()
            ->name('Test Product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 99.99, 19.0));

    // Use reflection to modify internal state
    $reflection = new ReflectionClass($builder);
    $dataProperty = $reflection->getProperty('data');
    $dataProperty->setAccessible(true);
    $data = $dataProperty->getValue($builder);

    // Set invalid tax type
    $data['taxConditions'] = ['taxType' => 'invalid'];
    $dataProperty->setValue($builder, $data);

    expect(fn () => $builder->toValidatedArray())
        ->toThrow(InvalidArgumentException::class, 'Invalid tax type. Must be one of: net, gross, vatfree');
});

test('invoice builder validation handles shipping conditions validation', function () {
    $builder = InvoiceBuilder::make()
        ->address(AddressBuilder::make()->name('Test Company')->street('Test Street')->city('Test City')->zip('12345')->countryCode('DE'))
        ->addLineItem(LineItemBuilder::custom()
            ->name('Test Product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 99.99, 19.0));

    // Use reflection to modify internal state
    $reflection = new ReflectionClass($builder);
    $dataProperty = $reflection->getProperty('data');
    $dataProperty->setAccessible(true);
    $data = $dataProperty->getValue($builder);

    // Set invalid shipping type
    $data['shippingConditions'] = [
        'shippingDate' => '2024-01-01',
        'shippingType' => 'invalid',
    ];
    $dataProperty->setValue($builder, $data);

    expect(fn () => $builder->toValidatedArray())
        ->toThrow(InvalidArgumentException::class, 'Invalid shipping type. Must be one of: delivery, service');
});

test('invoice builder validation handles empty shipping date', function () {
    $builder = InvoiceBuilder::make()
        ->address(AddressBuilder::make()->name('Test Company')->street('Test Street')->city('Test City')->zip('12345')->countryCode('DE'))
        ->addLineItem(LineItemBuilder::custom()
            ->name('Test Product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 99.99, 19.0));

    // Use reflection to modify internal state
    $reflection = new ReflectionClass($builder);
    $dataProperty = $reflection->getProperty('data');
    $dataProperty->setAccessible(true);
    $data = $dataProperty->getValue($builder);

    // Set empty shipping date
    $data['shippingConditions'] = [
        'shippingDate' => '',
        'shippingType' => 'delivery',
    ];
    $dataProperty->setValue($builder, $data);

    expect(fn () => $builder->toValidatedArray())
        ->toThrow(InvalidArgumentException::class, 'Shipping date is required.');
});

test('invoice builder toArray returns data directly', function () {
    $builder = InvoiceBuilder::make()
        ->address(AddressBuilder::make()->name('Test Company'))
        ->addLineItem(LineItemBuilder::custom()
            ->name('Test Product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 99.99, 19.0));

    $data = $builder->toArray();

    expect($data)->toBeArray()
        ->toHaveKey('address')
        ->toHaveKey('lineItems');
});

test('invoice builder toArray method returns data property', function () {
    $builder = InvoiceBuilder::make()
        ->address(AddressBuilder::make()->name('Test Company'))
        ->addLineItem(LineItemBuilder::custom()
            ->name('Test Product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 99.99, 19.0));

    // Test the toArray method specifically (line 200)
    $data = $builder->toArray();

    expect($data)->toBeArray()
        ->toHaveKey('address')
        ->toHaveKey('lineItems');
});

test('invoice builder toArray method line 200 coverage', function () {
    $builder = InvoiceBuilder::make()
        ->address(AddressBuilder::make()->name('Test Company'))
        ->addLineItem(LineItemBuilder::custom()
            ->name('Test Product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 99.99, 19.0));

    // Multiple calls to ensure line 200 is hit
    $data1 = $builder->toArray();
    $data2 = $builder->toArray();
    $data3 = $builder->toArray();

    expect($data1)->toBeArray()
        ->and($data2)->toBeArray()
        ->and($data3)->toBeArray();
});

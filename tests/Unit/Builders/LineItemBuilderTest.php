<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Builders\InvoiceBuilder;
use PhilHarmonie\LexOffice\Builders\LineItemBuilder;

test('it creates a custom line item', function () {
    $data = LineItemBuilder::custom()
        ->name('Test Product')
        ->quantity(1)
        ->unitName('piece')
        ->unitPrice('EUR', 99.99, 19.0)
        ->discountPercentage(0.0)
        ->toArray();

    expect($data)
        ->toHaveKey('type', 'custom')
        ->toHaveKey('name', 'Test Product')
        ->toHaveKey('quantity', 1)
        ->toHaveKey('unitName', 'piece')
        ->toHaveKey('unitPrice', [
            'currency' => 'EUR',
            'netAmount' => 99.99,
            'taxRatePercentage' => 19.0,
        ])
        ->toHaveKey('discountPercentage', 0.0);
});

test('it creates a text line item', function () {
    $data = LineItemBuilder::text()
        ->name('Test Text')
        ->description('Test Description')
        ->toArray();

    expect($data)
        ->toHaveKey('type', 'text')
        ->toHaveKey('name', 'Test Text')
        ->toHaveKey('description', 'Test Description');
});

test('it throws exception when setting quantity on text item', function () {
    expect(fn () => LineItemBuilder::text()->quantity(1))
        ->toThrow(InvalidArgumentException::class, 'Quantity can only be set for custom items.');
});

test('it throws exception when setting unit name on text item', function () {
    expect(fn () => LineItemBuilder::text()->unitName('piece'))
        ->toThrow(InvalidArgumentException::class, 'Unit name can only be set for custom items.');
});

test('it throws exception when setting unit price on text item', function () {
    expect(fn () => LineItemBuilder::text()->unitPrice('EUR', 99.99, 19.0))
        ->toThrow(InvalidArgumentException::class, 'Unit price can only be set for custom items.');
});

test('it throws exception when setting discount on text item', function () {
    expect(fn () => LineItemBuilder::text()->discountPercentage(0.0))
        ->toThrow(InvalidArgumentException::class, 'Discount can only be set for custom items.');
});

test('it allows description to be optional', function () {
    $data = LineItemBuilder::text()
        ->name('Test Text')
        ->toArray();

    expect($data)
        ->toHaveKey('type', 'text')
        ->toHaveKey('name', 'Test Text')
        ->not->toHaveKey('description');
});

test('it throws exception for invalid line item type', function () {
    $reflection = new ReflectionClass(LineItemBuilder::class);
    $constructor = $reflection->getConstructor();
    $constructor->setAccessible(true);

    $instance = $reflection->newInstanceWithoutConstructor();

    expect(fn () => $constructor->invoke($instance, 'invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid line item type.');
});

test('it creates custom item without optional fields', function () {
    $data = LineItemBuilder::custom()
        ->name('Test Product')
        ->quantity(1)
        ->unitName('piece')
        ->unitPrice('EUR', 99.99, 19.0)
        ->toArray();

    expect($data)
        ->toHaveKey('type', 'custom')
        ->toHaveKey('name', 'Test Product')
        ->not->toHaveKey('description')
        ->not->toHaveKey('discountPercentage');
});

test('it creates text item with null description', function () {
    $data = LineItemBuilder::text()
        ->name('Test Text')
        ->description(null)
        ->toArray();

    expect($data)
        ->toHaveKey('type', 'text')
        ->toHaveKey('name', 'Test Text')
        ->not->toHaveKey('description');
});

test('it handles partial payment discount conditions', function () {
    $data1 = InvoiceBuilder::make()
        ->paymentConditions('30 days', 30, 2.0)
        ->toArray();

    expect($data1['paymentConditions'])
        ->toBe([
            'paymentTermLabel' => '30 days',
            'paymentTermDuration' => 30,
        ])
        ->not->toHaveKey('paymentDiscountConditions');

    $data2 = InvoiceBuilder::make()
        ->paymentConditions('30 days', 30, null, 10)
        ->toArray();

    expect($data2['paymentConditions'])
        ->toBe([
            'paymentTermLabel' => '30 days',
            'paymentTermDuration' => 30,
        ])
        ->not->toHaveKey('paymentDiscountConditions');
});

test('it initializes empty line items array before first item', function () {
    $data = InvoiceBuilder::make();

    if (! isset($data->toArray()['lineItems'])) {
        $data->addLineItem(LineItemBuilder::text()->name('Test'));
        expect($data->toArray()['lineItems'])->toBeArray()->toHaveCount(1);
    }
});

test('it allows multiple line items of different types', function () {
    $data = InvoiceBuilder::make()
        ->addLineItem(LineItemBuilder::custom()
            ->name('Product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 10.0, 19.0))
        ->addLineItem(LineItemBuilder::text()
            ->name('Note'))
        ->addLineItem([
            'type' => 'custom',
            'name' => 'Raw Item',
            'quantity' => 1,
            'unitName' => 'piece',
            'unitPrice' => [
                'currency' => 'EUR',
                'netAmount' => 10.0,
                'taxRatePercentage' => 19.0,
            ],
        ])
        ->toArray();

    expect($data['lineItems'])
        ->toHaveCount(3)
        ->sequence(
            fn ($item) => $item->toHaveKey('type', 'custom'),
            fn ($item) => $item->toHaveKey('type', 'text'),
            fn ($item) => $item->toHaveKey('type', 'custom')
        );
});

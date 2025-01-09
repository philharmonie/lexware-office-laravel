<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Builders\AddressBuilder;
use PhilHarmonie\LexOffice\Builders\InvoiceBuilder;
use PhilHarmonie\LexOffice\Builders\LineItemBuilder;

beforeEach(function () {
    $this->invoice = InvoiceBuilder::make();
    $this->date = new DateTime('2024-01-01 12:00:00', new DateTimeZone('Europe/Berlin'));
});

test('it creates invoice with default values', function () {
    $data = InvoiceBuilder::make()->toArray();

    expect($data)
        ->toBeArray()
        ->toHaveKey('archived', false)
        ->toHaveKey('totalPrice')
        ->and($data['totalPrice'])->toBe(['currency' => 'EUR']);
});

test('it sets archived status', function () {
    $data = InvoiceBuilder::make()
        ->archived(true)
        ->toArray();

    expect($data['archived'])->toBeTrue();
});

test('it sets voucher date', function () {
    $data = $this->invoice
        ->voucherDate($this->date)
        ->toArray();

    expect($data['voucherDate'])->toBe('2024-01-01T12:00:00.000+01:00');
});

test('it sets address from builder', function () {
    $address = AddressBuilder::make()
        ->name('Test Company')
        ->street('Test Street 123')
        ->city('Test City')
        ->zip('12345')
        ->countryCode('DE');

    $data = InvoiceBuilder::make()
        ->address($address)
        ->toArray();

    expect($data['address'])->toHaveKey('name', 'Test Company')
        ->toHaveKey('supplement', '') // Default value
        ->toHaveKey('street', 'Test Street 123')
        ->toHaveKey('city', 'Test City')
        ->toHaveKey('zip', '12345')
        ->toHaveKey('countryCode', 'DE');
});

test('it sets address from array', function () {
    $address = [
        'name' => 'Test Company',
        'street' => 'Test Street 123',
        'city' => 'Test City',
        'zip' => '12345',
        'countryCode' => 'DE',
    ];

    $data = $this->invoice
        ->address($address)
        ->toArray();

    expect($data['address'])->toBe($address);
});

test('it sets line items', function () {
    $data = $this->invoice
        ->addLineItem(LineItemBuilder::custom()
            ->name('Test Product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 99.99, 19.0))
        ->addLineItem(LineItemBuilder::text()
            ->name('Note')
            ->description('Test note'))
        ->toArray();

    expect($data['lineItems'])
        ->toHaveCount(2)
        ->sequence(
            fn ($item) => $item
                ->toHaveKey('type', 'custom')
                ->toHaveKey('name', 'Test Product'),
            fn ($item) => $item
                ->toHaveKey('type', 'text')
                ->toHaveKey('name', 'Note')
        );
});

test('it sets tax conditions', function () {
    $data = $this->invoice
        ->taxConditions('net')
        ->toArray();

    expect($data['taxConditions'])->toBe(['taxType' => 'net']);
});

test('it throws exception for invalid tax type', function () {
    expect(fn () => $this->invoice->taxConditions('invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid tax type.');
});

test('it sets payment conditions', function () {
    $data = $this->invoice
        ->paymentConditions('10 days - 2%', 30, 2.0, 10)
        ->toArray();

    expect($data['paymentConditions'])->toBe([
        'paymentTermLabel' => '10 days - 2%',
        'paymentTermDuration' => 30,
        'paymentDiscountConditions' => [
            'discountPercentage' => 2.0,
            'discountRange' => 10,
        ],
    ]);
});

test('it sets shipping conditions', function () {
    $data = $this->invoice
        ->shippingConditions($this->date)
        ->toArray();

    expect($data['shippingConditions'])->toBe([
        'shippingDate' => '2024-01-01T12:00:00.000+01:00',
        'shippingType' => 'delivery',
    ]);
});

test('it throws exception for invalid shipping type', function () {
    expect(fn () => $this->invoice->shippingConditions($this->date, 'invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid shipping type.');
});

test('it sets basic text fields', function () {
    $data = $this->invoice
        ->title('Test Invoice')
        ->introduction('Test Introduction')
        ->remark('Test Remark')
        ->toArray();

    expect($data)
        ->toHaveKey('title', 'Test Invoice')
        ->toHaveKey('introduction', 'Test Introduction')
        ->toHaveKey('remark', 'Test Remark');
});

test('it sets voucher date from DateTimeImmutable', function () {
    $immutableDate = new DateTimeImmutable('2024-01-01 12:00:00', new DateTimeZone('Europe/Paris'));

    $data = $this->invoice
        ->voucherDate($immutableDate)
        ->toArray();

    expect($data['voucherDate'])->toBe('2024-01-01T12:00:00.000+01:00');
});

test('it sets payment conditions without discount', function () {
    $data = $this->invoice
        ->paymentConditions('Net 30', 30)
        ->toArray();

    expect($data['paymentConditions'])
        ->toBe([
            'paymentTermLabel' => 'Net 30',
            'paymentTermDuration' => 30,
        ]);
});

test('it sets multiple line items', function () {
    $data = $this->invoice
        ->addLineItem(
            LineItemBuilder::custom()
                ->name('Product 1')
                ->quantity(1)
                ->unitName('piece')
                ->unitPrice('EUR', 99.99, 19.0)
        )
        ->addLineItem(
            LineItemBuilder::text()
                ->name('Note')
                ->description('Additional information')
        )
        ->toArray();

    expect($data['lineItems'])
        ->toHaveCount(2)
        ->and($data['lineItems'][0]['type'])->toBe('custom')
        ->and($data['lineItems'][1]['type'])->toBe('text');
});

test('it sets shipping conditions with default type', function () {
    $data = $this->invoice
        ->shippingConditions($this->date)
        ->toArray();

    expect($data['shippingConditions'])
        ->toBe([
            'shippingDate' => '2024-01-01T12:00:00.000+01:00',
            'shippingType' => 'delivery',
        ]);
});

test('it combines all optional fields', function () {
    $data = $this->invoice
        ->title('Invoice Title')
        ->introduction('Invoice Introduction')
        ->remark('Invoice Remark')
        ->toArray();

    expect($data)
        ->toHaveKey('title', 'Invoice Title')
        ->toHaveKey('introduction', 'Invoice Introduction')
        ->toHaveKey('remark', 'Invoice Remark');
});

test('it sets timezone using string', function () {
    $data = $this->invoice
        ->timezone('Europe/London')
        ->voucherDate($this->date)
        ->toArray();

    expect($data['voucherDate'])->toBe('2024-01-01T11:00:00.000+00:00');
});

test('it sets timezone using DateTimeZone object', function () {
    $data = $this->invoice
        ->timezone(new DateTimeZone('Europe/London'))
        ->voucherDate($this->date)
        ->toArray();

    expect($data['voucherDate'])->toBe('2024-01-01T11:00:00.000+00:00');
});

test('it clears timezone when setting null', function () {
    $data = $this->invoice
        ->timezone('Europe/London')
        ->timezone(null)
        ->voucherDate($this->date)
        ->toArray();

    expect($data['voucherDate'])->toBe('2024-01-01T12:00:00.000+01:00');
});

test('timezone affects both voucher and shipping dates', function () {
    $data = $this->invoice
        ->timezone('Europe/London')
        ->voucherDate($this->date)
        ->shippingConditions($this->date)
        ->toArray();

    expect($data['voucherDate'])->toBe('2024-01-01T11:00:00.000+00:00')
        ->and($data['shippingConditions']['shippingDate'])->toBe('2024-01-01T11:00:00.000+00:00');
});

test('timezone handling with immutable dates', function () {
    $immutableDate = new DateTimeImmutable('2024-01-01 12:00:00', new DateTimeZone('Europe/Berlin'));

    $data = $this->invoice
        ->timezone('Europe/London')
        ->voucherDate($immutableDate)
        ->shippingConditions($immutableDate)
        ->toArray();

    expect($data['voucherDate'])->toBe('2024-01-01T11:00:00.000+00:00')
        ->and($data['shippingConditions']['shippingDate'])->toBe('2024-01-01T11:00:00.000+00:00');
});

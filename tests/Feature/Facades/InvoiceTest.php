<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Facades\Invoice;
use PhilHarmonie\LexOffice\Services\InvoiceService;

test('invoice facade resolves correct service', function () {
    expect(Invoice::getFacadeRoot())->toBeInstanceOf(InvoiceService::class);
});

test('invoice facade withoutCache method returns new service instance', function () {
    $originalService = Invoice::getFacadeRoot();
    $withoutCacheService = Invoice::withoutCache();

    expect($withoutCacheService)->not->toBe($originalService)
        ->and($withoutCacheService)->toBeInstanceOf(InvoiceService::class);
});

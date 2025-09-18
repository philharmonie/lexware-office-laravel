<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Facades\Contact;
use PhilHarmonie\LexOffice\Services\ContactService;

test('contact facade resolves correct service', function () {
    expect(Contact::getFacadeRoot())->toBeInstanceOf(ContactService::class);
});

test('contact facade withoutCache method returns new service instance', function () {
    $originalService = Contact::getFacadeRoot();
    $withoutCacheService = Contact::withoutCache();

    expect($withoutCacheService)->not->toBe($originalService)
        ->and($withoutCacheService)->toBeInstanceOf(ContactService::class);
});

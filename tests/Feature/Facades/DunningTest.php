<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Facades\Dunning;
use PhilHarmonie\LexOffice\Services\DunningService;

test('dunning facade resolves correct service', function () {
    expect(Dunning::getFacadeRoot())->toBeInstanceOf(DunningService::class);
});

test('dunning facade withoutCache method returns new service instance', function () {
    $originalService = Dunning::getFacadeRoot();
    $withoutCacheService = Dunning::withoutCache();

    expect($withoutCacheService)->not->toBe($originalService)
        ->and($withoutCacheService)->toBeInstanceOf(DunningService::class);
});

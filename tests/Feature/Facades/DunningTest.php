<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Facades\Dunning;
use PhilHarmonie\LexOffice\Services\DunningService;

test('dunning facade resolves correct service', function () {
    expect(Dunning::getFacadeRoot())->toBeInstanceOf(DunningService::class);
});

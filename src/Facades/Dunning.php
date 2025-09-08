<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Facades;

use Illuminate\Support\Facades\Facade;
use PhilHarmonie\LexOffice\Services\DunningService;

final class Dunning extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DunningService::class;
    }
}

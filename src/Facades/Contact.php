<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Facades;

use Illuminate\Support\Facades\Facade;
use PhilHarmonie\LexOffice\Services\ContactService;

final class Contact extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ContactService::class;
    }
}

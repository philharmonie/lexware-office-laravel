<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Facades;

use Illuminate\Support\Facades\Facade;
use PhilHarmonie\LexOffice\Services\InvoiceService;

final class Invoice extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return InvoiceService::class;
    }
}

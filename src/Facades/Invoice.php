<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Facades;

use Illuminate\Support\Facades\Facade;
use PhilHarmonie\LexOffice\Services\InvoiceService;

 * @method static array create(array $data, bool $finalize = false)
 * @method static array find(string $id)
 *
 * @mixin InvoiceService
 */
final class Invoice extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return InvoiceService::class;
    }
}

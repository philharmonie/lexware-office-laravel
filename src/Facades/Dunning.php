<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Facades;

use Illuminate\Support\Facades\Facade;
use PhilHarmonie\LexOffice\Services\DunningService;

/**
 * @method static array create(array $data)
 * @method static array find(string $id)
 * @method static array pursue(string $id)
 * @method static array render(string $id)
 * @method static array download(string $id)
 * @method static array deeplink(string $id)
 *
 * @mixin DunningService
 */
final class Dunning extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DunningService::class;
    }
}

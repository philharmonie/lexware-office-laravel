<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Facades;

use Illuminate\Support\Facades\Facade;
use PhilHarmonie\LexOffice\Services\ArticleService;

/**
 * @method static array find(string $id)
 * @method static array list(array $filters = [])
 * @method static array create(array $article)
 * @method static array update(string $id, array $article)
 * @method static ArticleService withoutCache()
 *
 * @mixin ArticleService
 */
final class Article extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ArticleService::class;
    }
}

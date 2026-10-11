<?php

declare(strict_types=1);

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use PhilHarmonie\LexOffice\Facades\Article;
use PhilHarmonie\LexOffice\Services\ArticleService;

test('article facade resolves correct service', function () {
    expect(Article::getFacadeRoot())->toBeInstanceOf(ArticleService::class);
});

test('article facade lists articles through the configured client', function () {
    app(Factory::class)->preventStrayRequests();
    app(Factory::class)->fake([
        'api.lexware.io/v1/articles*' => Factory::response(['content' => [], 'totalPages' => 0]),
    ]);

    $result = Article::list(['type' => 'SERVICE']);

    app(Factory::class)->assertSent(fn (Request $request): bool => $request->url() === 'https://api.lexware.io/v1/articles?type=SERVICE'
        && $request->hasHeader('Authorization', 'Bearer test-api-key'));

    expect($result)->toBe(['content' => [], 'totalPages' => 0]);
});

test('article facade withoutCache method returns new service instance', function () {
    $originalService = Article::getFacadeRoot();
    $withoutCacheService = Article::withoutCache();

    expect($withoutCacheService)->not->toBe($originalService)
        ->and($withoutCacheService)->toBeInstanceOf(ArticleService::class);
});

<?php

declare(strict_types=1);

use Illuminate\Cache\CacheManager;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use PhilHarmonie\LexOffice\Client;
use PhilHarmonie\LexOffice\Contracts\ClientInterface;
use PhilHarmonie\LexOffice\Services\ArticleService;

function articlePayload(array $overrides = []): array
{
    return array_replace([
        'id' => 'eb46d328-e1dd-11eb-8f3c-ef6e1f3a1a9c',
        'organizationId' => '7b04aeb2-e1d2-11eb-a5a6-4b5b3c4d5e6f',
        'createdDate' => '2023-07-08T12:00:00.000+02:00',
        'updatedDate' => '2023-07-08T12:00:00.000+02:00',
        'archived' => false,
        'title' => 'Lexware buch',
        'description' => 'Ein Buch',
        'type' => 'PRODUCT',
        'articleNumber' => 'LXW-BUCH-001',
        'gtin' => '9783648170632',
        'note' => 'Interne Notiz',
        'unitName' => 'Stück',
        'price' => [
            'netPrice' => 61.90,
            'grossPrice' => 73.66,
            'leadingPrice' => 'NET',
            'taxRate' => 19,
        ],
        'version' => 1,
    ], $overrides);
}

function articlePage(array $content): array
{
    return [
        'content' => $content,
        'first' => true,
        'last' => true,
        'totalPages' => 1,
        'totalElements' => count($content),
        'numberOfElements' => count($content),
        'size' => 25,
        'number' => 0,
        'sort' => [],
    ];
}

beforeEach(function () {
    $this->http = new Factory;
    $this->http->preventStrayRequests();
    $this->service = new ArticleService(new Client('test-api-key', $this->http));
});

test('list hits GET /articles with paging and filters', function () {
    $page = articlePage([articlePayload()]);

    $this->http->fake([
        'api.lexware.io/v1/articles*' => $this->http::response($page),
    ]);

    $result = $this->service->list([
        'page' => 0,
        'size' => 25,
        'articleNumber' => 'LXW-BUCH-001',
        'gtin' => '9783648170632',
        'type' => 'PRODUCT',
    ]);

    $this->http->assertSent(function (Request $request): bool {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

        return $request->method() === 'GET'
            && str_starts_with($request->url(), 'https://api.lexware.io/v1/articles?')
            && $request->hasHeader('Authorization', 'Bearer test-api-key')
            && $query === [
                'page' => '0',
                'size' => '25',
                'articleNumber' => 'LXW-BUCH-001',
                'gtin' => '9783648170632',
                'type' => 'PRODUCT',
            ];
    });

    expect($result)->toBe($page)
        ->and($result['content'][0]['price']['taxRate'])->toBe(19);
});

test('list without filters hits GET /articles', function () {
    $this->http->fake([
        '*' => $this->http::response(articlePage([])),
    ]);

    $result = $this->service->list();

    $this->http->assertSent(fn (Request $request): bool => $request->url() === 'https://api.lexware.io/v1/articles');

    expect($result['content'])->toBe([]);
});

test('find hits GET /articles/{id}', function () {
    $article = articlePayload();

    $this->http->fake([
        'api.lexware.io/v1/articles/'.$article['id'] => $this->http::response($article),
    ]);

    $result = $this->service->find($article['id']);

    $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://api.lexware.io/v1/articles/'.$article['id']);

    expect($result)->toBe($article);
});

test('articles are never served from cache', function () {
    $cache = app(CacheManager::class);
    $service = new ArticleService(new Client('test-api-key', $this->http, $cache));

    $this->http->fake([
        '*' => $this->http->sequence()
            ->push(articlePage([articlePayload(['version' => 1])]))
            ->push(articlePage([articlePayload(['version' => 2])])),
    ]);

    $first = $service->list();
    $second = $service->list();

    $this->http->assertSentCount(2);

    expect($first['content'][0]['version'])->toBe(1)
        ->and($second['content'][0]['version'])->toBe(2);
});

test('withoutCache returns a new article service', function () {
    $withoutCache = $this->service->withoutCache();

    expect($withoutCache)->toBeInstanceOf(ArticleService::class)
        ->not->toBe($this->service);
});

test('services can be built manually with per-tenant credentials', function () {
    $this->http->fake([
        '*' => $this->http::response(articlePage([])),
    ]);

    $tenantA = new ArticleService(new Client(apiKey: 'tenant-a-key', http: $this->http, cache: app(CacheManager::class)));
    $tenantB = new ArticleService(new Client(apiKey: 'tenant-b-key', http: $this->http, cache: app(CacheManager::class)));

    $tenantA->list();
    $tenantB->list();

    $this->http->assertSent(fn (Request $request): bool => $request->hasHeader('Authorization', 'Bearer tenant-a-key'));
    $this->http->assertSent(fn (Request $request): bool => $request->hasHeader('Authorization', 'Bearer tenant-b-key'));
});

test('cached responses are not shared between api keys', function () {
    $cache = app(CacheManager::class);

    $this->http->fake([
        '*' => $this->http->sequence()
            ->push(['content' => [['id' => 'a']]])
            ->push(['content' => [['id' => 'b']]]),
    ]);

    $tenantA = new Client(apiKey: 'tenant-a-key', http: $this->http, cache: $cache);
    $tenantB = new Client(apiKey: 'tenant-b-key', http: $this->http, cache: $cache);

    expect($tenantA->get('/contacts')['content'][0]['id'])->toBe('a')
        ->and($tenantB->get('/contacts')['content'][0]['id'])->toBe('b')
        ->and($tenantA->get('/contacts')['content'][0]['id'])->toBe('a');

    $this->http->assertSentCount(2);
});

function articleWritePayload(): array
{
    return [
        'title' => 'Lexware buch',
        'description' => 'Ein Buch',
        'type' => 'PRODUCT',
        'articleNumber' => 'LXW-BUCH-001',
        'gtin' => '9783648170632',
        'note' => 'Interne Notiz',
        'unitName' => 'Stück',
        'price' => [
            'netPrice' => 61.90,
            'leadingPrice' => 'NET',
            'taxRate' => 19,
        ],
    ];
}

function articleWriteResponse(int $version): array
{
    return [
        'id' => 'eb46d328-e1dd-11eb-8f3c-ef6e1f3a1a9c',
        'resourceUri' => 'https://api.lexware.io/v1/articles/eb46d328-e1dd-11eb-8f3c-ef6e1f3a1a9c',
        'createdDate' => '2023-07-08T12:00:00.000+02:00',
        'updatedDate' => '2023-07-08T12:00:00.000+02:00',
        'version' => $version,
    ];
}

test('create posts the article to /articles', function () {
    $payload = articleWritePayload();
    $response = articleWriteResponse(0);

    $this->http->fake([
        'api.lexware.io/v1/articles' => $this->http::response($response, 201),
    ]);

    $result = $this->service->create($payload);

    $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api.lexware.io/v1/articles'
        && $request->hasHeader('Authorization', 'Bearer test-api-key')
        && $request->data() === $payload);
    $this->http->assertSentCount(1);

    expect($result)->toBe($response);
});

test('update reads the current version uncached and puts it with the article', function () {
    $cache = app(CacheManager::class);
    $service = new ArticleService(new Client('test-api-key', $this->http, $cache));
    $id = 'eb46d328-e1dd-11eb-8f3c-ef6e1f3a1a9c';
    $payload = articleWritePayload();
    $response = articleWriteResponse(4);

    $this->http->fake([
        'api.lexware.io/v1/articles/'.$id => $this->http->sequence()
            ->push(articlePayload(['version' => 3]))
            ->push($response),
    ]);

    $result = $service->update($id, $payload);

    $recorded = $this->http->recorded();

    expect($recorded)->toHaveCount(2)
        ->and($recorded[0][0]->method())->toBe('GET')
        ->and($recorded[0][0]->url())->toBe('https://api.lexware.io/v1/articles/'.$id)
        ->and($recorded[1][0]->method())->toBe('PUT')
        ->and($recorded[1][0]->url())->toBe('https://api.lexware.io/v1/articles/'.$id)
        ->and($recorded[1][0]->data())->toBe([...$payload, 'version' => 3])
        ->and($result)->toBe($response)
        ->and($cache->get('lexoffice:get:'.md5('test-api-key|/articles/'.$id.serialize([]))))->toBeNull();
});

test('update keeps an explicitly passed version and skips the read', function () {
    $id = 'eb46d328-e1dd-11eb-8f3c-ef6e1f3a1a9c';
    $payload = [...articleWritePayload(), 'version' => 7];
    $response = articleWriteResponse(8);

    $this->http->fake([
        'api.lexware.io/v1/articles/'.$id => $this->http::response($response),
    ]);

    $result = $this->service->update($id, $payload);

    $this->http->assertSentCount(1);
    $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PUT'
        && $request->url() === 'https://api.lexware.io/v1/articles/'.$id
        && $request->data() === $payload);

    expect($result)->toBe($response);
});

test('update reads the version without cache even for cached clients', function () {
    $client = Mockery::mock(ClientInterface::class);
    $uncached = Mockery::mock(ClientInterface::class);
    $client->shouldReceive('withoutCache')->once()->andReturn($uncached);
    $client->shouldNotReceive('get');
    $uncached->shouldReceive('get')->once()->with('/articles/abc')->andReturn(['version' => 2]);
    $client->shouldReceive('put')->once()->with('/articles/abc', ['title' => 'X', 'version' => 2])->andReturn(['version' => 3]);

    expect((new ArticleService($client))->update('abc', ['title' => 'X']))->toBe(['version' => 3]);
});

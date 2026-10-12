<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Services;

use PhilHarmonie\LexOffice\Contracts\ClientInterface;

final readonly class ArticleService
{
    public function __construct(
        private ClientInterface $client
    ) {}

    public function withoutCache(): self
    {
        return new self($this->client->withoutCache());
    }

    /**
     * Retrieve a single article by its id.
     *
     * @return array<string, mixed>
     */
    public function find(string $id): array
    {
        return $this->client->get("/articles/{$id}");
    }

    /**
     * List articles as a paged result (`content`, `totalPages`, `number`, ...).
     *
     * Supports the paging params `page` and `size` and the filters
     * `articleNumber`, `gtin` and `type` (PRODUCT or SERVICE).
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = []): array
    {
        return $this->client->get('/articles', $filters);
    }

    /**
     * Create an article via POST /articles.
     *
     * Expected payload: `title` (required), `description`, `type`
     * ('PRODUCT'|'SERVICE', required), `articleNumber`, `gtin`, `note`,
     * `unitName` (required) and `price` (required) with `netPrice` (float),
     * `leadingPrice` ('NET') and `taxRate` (e.g. 19).
     *
     * Returns the API response (`id`, `resourceUri`, `createdDate`,
     * `updatedDate`, `version`).
     *
     * @param  array<string, mixed>  $article
     * @return array<string, mixed>
     */
    public function create(array $article): array
    {
        return $this->client->post('/articles', $article);
    }

    /**
     * Update an article via PUT /articles/{id}.
     *
     * Lexware uses optimistic locking: the PUT body must carry the article's
     * current `version`. Unless the caller passes a `version`, the current
     * article is fetched first, always bypassing the cache, so the version
     * is never stale. Takes the same payload as {@see create()}.
     *
     * Returns the API response (`id`, `resourceUri`, `createdDate`,
     * `updatedDate`, `version`).
     *
     * @param  array<string, mixed>  $article
     * @return array<string, mixed>
     */
    public function update(string $id, array $article): array
    {
        if (! array_key_exists('version', $article)) {
            $current = $this->client->withoutCache()->get("/articles/{$id}");
            $article['version'] = $current['version'] ?? null;
        }

        return $this->client->put("/articles/{$id}", $article);
    }
}

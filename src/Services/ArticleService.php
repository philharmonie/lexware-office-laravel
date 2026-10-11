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
}

<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Services;

use PhilHarmonie\LexOffice\Contracts\ClientInterface;

final readonly class ContactService
{
    public function __construct(
        private ClientInterface $client
    ) {}

    public function withoutCache(): self
    {
        return new self($this->client->withoutCache());
    }

    /**
     * @return array<string, mixed>
     */
    public function find(string $id): array
    {
        return $this->client->get("/contacts/{$id}");
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = []): array
    {
        return $this->client->get('/contacts', $filters);
    }
}

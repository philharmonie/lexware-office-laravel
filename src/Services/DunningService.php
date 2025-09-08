<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Services;

use PhilHarmonie\LexOffice\Contracts\ClientInterface;

final readonly class DunningService
{
    public function __construct(
        private ClientInterface $client
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        return $this->client->post('/dunnings', $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function find(string $id): array
    {
        return $this->client->get("/dunnings/{$id}");
    }

    /**
     * @return array<string, mixed>
     */
    public function pursue(string $id): array
    {
        return $this->client->post("/dunnings/{$id}/pursue");
    }

    /**
     * @return array<string, mixed>
     */
    public function render(string $id): array
    {
        return $this->client->post("/dunnings/{$id}/document");
    }

    /**
     * @return array<string, mixed>
     */
    public function download(string $id): array
    {
        return $this->client->get("/dunnings/{$id}/document");
    }

    /**
     * @return array<string, mixed>
     */
    public function deeplink(string $id): array
    {
        return $this->client->get("/dunnings/{$id}/deeplink");
    }
}

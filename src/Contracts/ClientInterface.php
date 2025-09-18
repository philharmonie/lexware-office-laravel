<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Contracts;

interface ClientInterface
{
    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $params = []): array;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array;

    /**
     * Create a new client instance that bypasses cache for subsequent requests.
     */
    public function withoutCache(): self;
}

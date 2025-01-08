<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Services;

use PhilHarmonie\LexOffice\Contracts\ClientInterface;

final readonly class InvoiceService
{
    public function __construct(
        private ClientInterface $client
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data, bool $finalize = false): array
    {
        $endpoint = '/invoices';
        if ($finalize) {
            $endpoint .= '?finalize=true';
        }

        return $this->client->post($endpoint, $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function find(string $id): array
    {
        return $this->client->get("/invoices/{$id}");
    }
}

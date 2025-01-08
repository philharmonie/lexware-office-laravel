<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice;

use Illuminate\Http\Client\Factory as Http;
use PhilHarmonie\LexOffice\Contracts\ClientInterface;
use PhilHarmonie\LexOffice\Exceptions\ApiException;
use Throwable;

final class Client implements ClientInterface
{
    private string $baseUrl = 'https://api.lexoffice.io/v1';

    public function __construct(
        private readonly string $apiKey,
        private readonly Http $http
    ) {}

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $params = []): array
    {
        try {
            /** @var array<string, mixed> */
            return $this->http
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl.$endpoint, $params)
                ->throw()
                ->json();
        } catch (Throwable $e) {
            throw new ApiException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array
    {
        try {
            /** @var array<string, mixed> */
            return $this->http
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl.$endpoint, $data)
                ->throw()
                ->json();
        } catch (Throwable $e) {
            throw new ApiException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}

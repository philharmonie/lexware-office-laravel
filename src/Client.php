<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice;

use Illuminate\Http\Client\Factory as Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use PhilHarmonie\LexOffice\Contracts\ClientInterface;
use PhilHarmonie\LexOffice\Exceptions\ApiException;

final class Client implements ClientInterface
{
    private string $baseUrl = 'https://api.lexoffice.io/v1';

    public function __construct(
        private readonly string $apiKey,
        private readonly Http $http
    ) {
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $params = []): array
    {
        try {
            return $this->http
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get($this->baseUrl.$endpoint, $params)
                ->throw()
                ->json();
        } catch (RequestException $e) {
            $this->logError($e, $endpoint, $params);
            throw $e;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array
    {
        try {
            return $this->http
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl.$endpoint, $data)
                ->throw()
                ->json();
        } catch (RequestException $e) {
            $this->logError($e, $endpoint, $data);
            throw $e;
        }
    }

    /**
     * @throws ApiException
     */
    private function logError(RequestException $e, string $endpoint, array $data): never
    {
        $response = $e->response;

        $status = $response->status();
        $message = $response->json('message') ?? $response->body() ?? $e->getMessage();

        Log::log($status >= 500 ? 'error' : 'warning', 'LexOffice API Error', [
            'status' => $status,
            'message' => $message,
            'endpoint' => $endpoint,
            'payload' => $data,
            'response' => $response->json(),
        ]);

        throw new ApiException("LexOffice Error $status: $message", $status, $e);
    }
}

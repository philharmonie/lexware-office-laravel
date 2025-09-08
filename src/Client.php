<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice;

use Illuminate\Http\Client\Factory as Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PhilHarmonie\LexOffice\Contracts\ClientInterface;
use PhilHarmonie\LexOffice\Exceptions\ApiException;

final readonly class Client implements ClientInterface
{
    public function __construct(
        private string $apiKey,
        private Http $http
    ) {}

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $params = []): array
    {
        try {
            $response = $this->http
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get($this->getBaseUrl().$endpoint, $params)
                ->throw()
                ->json();

            return is_array($response) ? $response : [];
        } catch (RequestException $e) {
            $this->logError($e, $endpoint, $params);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->http
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->getBaseUrl().$endpoint, $data)
                ->throw()
                ->json();

            return is_array($response) ? $response : [];
        } catch (RequestException $e) {
            $this->logError($e, $endpoint, $data);
        }
    }

    private function getBaseUrl(): string
    {
        $baseUrl = Config::get('lexoffice.base_url', 'https://api.lexoffice.io/v1');

        return is_string($baseUrl) ? $baseUrl : 'https://api.lexoffice.io/v1';
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws ApiException
     */
    private function logError(RequestException $e, string $endpoint, array $data): never
    {
        $response = $e->response;

        $status = $response->status();
        $jsonMessage = $response->json('message');
        $bodyMessage = $response->body();
        $exceptionMessage = $e->getMessage();

        $message = $jsonMessage ?? (empty($bodyMessage) ? $exceptionMessage : $bodyMessage);

        Log::log($status >= 500 ? 'error' : 'warning', 'LexOffice API Error', [
            'status' => $status,
            'message' => $message,
            'endpoint' => $endpoint,
            'payload' => $data,
            'response' => $response->json(),
        ]);

        $errorDetails = $response->json() ?? ['message' => $message];
        $formattedMessage = sprintf(
            'HTTP request returned status code %d:%s%s',
            $status,
            "\n",
            json_encode($errorDetails)
        );

        throw new ApiException($formattedMessage, $status, $e);
    }
}

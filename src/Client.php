<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice;

use Exception;
use Illuminate\Cache\CacheManager;
use Illuminate\Http\Client\Factory as Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PhilHarmonie\LexOffice\Contracts\ClientInterface;
use PhilHarmonie\LexOffice\Exceptions\ApiException;

final readonly class Client implements ClientInterface
{
    private const RATE_LIMIT_REQUESTS_PER_SECOND = 2;

    private const RATE_LIMIT_WINDOW_SECONDS = 1;

    private const MAX_RETRY_ATTEMPTS = 3;

    private const RETRY_DELAY_BASE_MS = 1000;

    public function __construct(
        private string $apiKey,
        private Http $http,
        private ?CacheManager $cache = null,
        private int $cacheTtl = 300
    ) {}

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $params = []): array
    {
        $cacheKey = $this->getCacheKey('GET', $endpoint, $params);

        if ($this->cache && $this->shouldCache($endpoint)) {
            $cached = $this->cache->get($cacheKey);
            if ($cached !== null && is_array($cached)) {
                return $cached;
            }
        }

        return $this->withRetry(function () use ($endpoint, $params, $cacheKey): array {
            $this->handleRateLimit();

            $response = $this->http
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get($this->getBaseUrl().$endpoint, $params)
                ->throw()
                ->json();

            $result = is_array($response) ? $response : [];

            if ($this->cache && $this->shouldCache($endpoint)) {
                $this->cache->put($cacheKey, $result, $this->cacheTtl);
            }

            return $result;
        }, $endpoint, $params);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->withRetry(function () use ($endpoint, $data): array {
            $this->handleRateLimit();

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
        }, $endpoint, $data);
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

        throw new ApiException($formattedMessage, $status, is_array($errorDetails) ? $errorDetails : ['message' => $message], $e);
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function getCacheKey(string $method, string $endpoint, array $params = []): string
    {
        return 'lexoffice:'.strtolower($method).':'.md5($endpoint.serialize($params));
    }

    private function shouldCache(string $endpoint): bool
    {
        // Cache GET requests for read-only endpoints
        $cacheableEndpoints = [
            '/contacts',
            '/countries',
            '/payment-conditions',
            '/posting-categories',
            '/print-layouts',
            '/profile',
            '/recurring-templates',
        ];

        foreach ($cacheableEndpoints as $cacheableEndpoint) {
            if (str_starts_with($endpoint, $cacheableEndpoint)) {
                return true;
            }
        }

        return false;
    }

    private function handleRateLimit(): void
    {
        if (! $this->cache instanceof CacheManager) {
            return;
        }

        $rateLimitKey = 'lexoffice:rate_limit:'.$this->apiKey;
        $currentTime = time();
        $windowStart = $currentTime - self::RATE_LIMIT_WINDOW_SECONDS;

        // Get request timestamps for this window
        $requestTimestamps = $this->cache->get($rateLimitKey, []);

        // Ensure we have an array
        if (! is_array($requestTimestamps)) {
            $requestTimestamps = [];
        }

        // Filter out timestamps outside the current window
        $requestTimestamps = array_filter($requestTimestamps, fn ($timestamp): bool => $timestamp > $windowStart);

        // Check if we're at the rate limit
        $requestCount = count($requestTimestamps);
        if ($requestCount >= self::RATE_LIMIT_REQUESTS_PER_SECOND) {
            $oldestRequest = min($requestTimestamps);
            $waitTime = $oldestRequest + self::RATE_LIMIT_WINDOW_SECONDS - $currentTime;

            if ($waitTime > 0) {
                usleep((int) ($waitTime * 1000000)); // Convert to microseconds
            }
        }

        // Add current request timestamp
        $requestTimestamps[] = $currentTime;

        // Store updated timestamps
        $this->cache->put($rateLimitKey, $requestTimestamps, self::RATE_LIMIT_WINDOW_SECONDS);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function withRetry(callable $operation, string $endpoint, array $payload): array
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < self::MAX_RETRY_ATTEMPTS) {
            try {
                return $operation();
            } catch (RequestException $e) {
                $lastException = $e;
                $attempt++;

                $isRetryable = $e->response !== null
                    && in_array($e->response->status(), [429, 500, 502, 503, 504], true);

                if ($isRetryable) {
                    if ($attempt < self::MAX_RETRY_ATTEMPTS) {
                        $delay = self::RETRY_DELAY_BASE_MS * 2 ** ($attempt - 1);
                        usleep($delay * 1000);

                        continue; // weiterer Versuch
                    }

                    // >>> Hier: Versuche erschöpft -> Schleife verlassen, damit unten lastException geworfen wird
                    break;
                }

                // Nicht-retryable: sofort Domain-Exception
                $this->logError($e, $endpoint, $payload);
            }
        }

        throw $lastException ?? new Exception('Unknown error occurred');
    }
}

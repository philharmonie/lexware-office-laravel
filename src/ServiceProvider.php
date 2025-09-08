<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice;

use Illuminate\Http\Client\Factory as Http;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use PhilHarmonie\LexOffice\Contracts\ClientInterface;
use PhilHarmonie\LexOffice\Services\ContactService;
use PhilHarmonie\LexOffice\Services\DunningService;
use PhilHarmonie\LexOffice\Services\InvoiceService;
use RuntimeException;

final class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/lexoffice.php',
            'lexoffice'
        );

        $this->app->singleton(Client::class, function ($app): Client {
            /** @var array{api_key: string|null} */
            $config = config('lexoffice');
            $apiKey = $config['api_key'];

            if (empty($apiKey) || ! is_string($apiKey)) {
                throw new RuntimeException('Invalid Lexware Office API key configuration.');
            }

            return new Client(
                apiKey: $apiKey,
                http: $app->make(Http::class)
            );
        });

        $this->app->bind(ClientInterface::class, Client::class);
        $this->app->singleton(ContactService::class);
        $this->app->singleton(DunningService::class);
        $this->app->singleton(InvoiceService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/lexoffice.php' => config_path('lexoffice.php'),
            ], 'lexoffice-config');
        }
    }
}

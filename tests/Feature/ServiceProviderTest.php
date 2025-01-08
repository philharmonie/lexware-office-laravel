<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Client;
use PhilHarmonie\LexOffice\Services\ContactService;
use PhilHarmonie\LexOffice\Services\InvoiceService;

test('service provider registers bindings', function () {
    expect(app(Client::class))->toBeInstanceOf(Client::class)
        ->and(app(ContactService::class))->toBeInstanceOf(ContactService::class)
        ->and(app(InvoiceService::class))->toBeInstanceOf(InvoiceService::class);
});

test('service provider requires api key', function () {
    config()->set('lexoffice.api_key', null);

    expect(fn () => app(Client::class))
        ->toThrow(RuntimeException::class, 'Invalid Lexware Office API key configuration.');
});

test('config file is published', function () {
    $this->artisan('vendor:publish', ['--tag' => 'lexoffice-config'])
        ->assertExitCode(0);

    expect(file_exists(config_path('lexoffice.php')))->toBeTrue();
});

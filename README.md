# Lexware Office Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/philharmonie/lexware-office-laravel.svg?style=flat-square)](https://packagist.org/packages/philharmonie/lexware-office-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/philharmonie/lexware-office-laravel.svg?style=flat-square)](https://packagist.org/packages/philharmonie/lexware-office-laravel)
[![License](https://img.shields.io/packagist/l/philharmonie/lexware-office-laravel.svg?style=flat-square)](https://packagist.org/packages/philharmonie/lexware-office-laravel)

A Laravel package for seamless integration with the Lexware Office API. This package provides an elegant way to interact with Lexware Office services, including contacts and invoices management.

## Requirements

- PHP ^8.2
- Laravel ^10.0|^11.0
- Guzzle ^7.0

## Installation

You can install the package via composer:

```bash
composer require philharmonie/lexware-office-laravel
```

### Service Provider

The service provider is automatically registered using Laravel's auto-discovery feature. If you need to register it manually, add the following line to the providers array in `config/app.php`:

```php
PhilHarmonie\LexOffice\ServiceProvider::class,
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="lexoffice-config"
```

Add your Lexware Office API key to your `.env` file:

```env
LEXOFFICE_API_KEY=your-api-key
```

## Usage

### Contacts

```php
// Find a contact by ID
$contact = app(ContactService::class)->find('contact-id');

// List contacts with optional filters
$contacts = app(ContactService::class)->list([
    'email' => 'example@domain.com'
]);
```

### Invoices

```php
// Create an invoice
$invoice = app(InvoiceService::class)->create([
    // Invoice data
], $finalize = false);

// Find an invoice by ID
$invoice = app(InvoiceServiceInterface::class)->find('invoice-id');
```

### Using the Facades

```php
use PhilHarmonie\LexOffice\Facades\Contact;use PhilHarmonie\LexOffice\Facades\Invoice;

// Find a contact
$contact = Contact::find('contact-id');

// List contacts
$contacts = Contact::list(['email' => 'example@domain.com']);

// Create an invoice
$invoice = Invoice::create($data, $finalize = false);

// Find an invoice
$invoice = Invoice::find('invoice-id');
```

### Direct Client Usage

If you need more control, you can use the client directly:

```php
use PhilHarmonie\LexOffice\Contracts\ClientInterface;

$client = app(ClientInterface::class);

// GET request
$response = $client->get('/contacts', ['email' => 'example@domain.com']);

// POST request
$response = $client->post('/invoices', ['data' => 'value']);
```

## Testing

```bash
composer test
```

This will run:

- Code style checks (Pint)
- Static analysis (PHPStan)
- Unit tests (Pest)
- Refactoring checks (Rector)

Individual test commands:

```bash
composer test:lint    # Run Laravel Pint
composer test:types   # Run PHPStan
composer test:unit    # Run Pest tests
composer test:refacto # Run Rector
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email phil@harmonie.media instead of using the issue tracker.

## Credits

- [Phil Harmonie](https://github.com/philharmonie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

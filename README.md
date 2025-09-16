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
LEXOFFICE_BASE_URL=https://api.lexware.io/v1
LEXOFFICE_TIMEOUT=30
LEXOFFICE_RETRY_ATTEMPTS=3
LEXOFFICE_CACHE_TTL=300
LEXOFFICE_RATE_LIMITING_ENABLED=true
LEXOFFICE_LOGGING_ENABLED=false
```

### Configuration Options

- `LEXOFFICE_API_KEY` - Your Lexware Office API key (required)
- `LEXOFFICE_BASE_URL` - API base URL (default: https://api.lexware.io/v1)
- `LEXOFFICE_TIMEOUT` - Request timeout in seconds (default: 30)
- `LEXOFFICE_RETRY_ATTEMPTS` - Number of retry attempts for failed requests (default: 3)
- `LEXOFFICE_CACHE_TTL` - Cache time-to-live in seconds (default: 300)
- `LEXOFFICE_RATE_LIMITING_ENABLED` - Enable automatic rate limiting (default: true)
- `LEXOFFICE_LOGGING_ENABLED` - Enable detailed API logging (default: false)

## Usage

### Contacts

```php
use PhilHarmonie\LexOffice\Facades\Contact;

// Find a contact by ID
$contact = Contact::find('contact-id');

// List contacts with optional filters
$contacts = Contact::list(['email' => 'example@domain.com']);
```

### Invoices

```php
use PhilHarmonie\LexOffice\Facades\Invoice;

// Create an invoice
$invoice = Invoice::create($data, $finalize = false);

// Find an invoice by ID
$invoice = Invoice::find('invoice-id');
```

### Dunning

```php
use PhilHarmonie\LexOffice\Facades\Dunning;

// Create a dunning
$dunning = Dunning::create($data);

// Find a dunning
$dunning = Dunning::find('dunning-id');

// Pursue a dunning
$result = Dunning::pursue('dunning-id');

// Render dunning document
$document = Dunning::render('dunning-id');

// Download dunning file
$file = Dunning::download('dunning-id');

// Get deeplink
$link = Dunning::deeplink('dunning-id');
```

### Using Builders

The package provides fluent builders for creating invoices and related structures with comprehensive validation:

```php
use PhilHarmonie\LexOffice\Builders\InvoiceBuilder;
use PhilHarmonie\LexOffice\Builders\AddressBuilder;
use PhilHarmonie\LexOffice\Builders\LineItemBuilder;

$invoice = InvoiceBuilder::make()
    ->timezone('Europe/Berlin')
    ->voucherDate(now())
    ->address(
        AddressBuilder::make()
            ->name('Company Name')
            ->supplement('c/o John Doe')
            ->street('Street 123')
            ->city('City')
            ->zip('12345')
            ->countryCode('DE')
    )
    ->addLineItem(
        LineItemBuilder::custom()
            ->name('Product')
            ->description('Detailed description of the product')
            ->quantity(1)
            ->unitName('piece')
            ->unitPrice('EUR', 99.99, 19.0)
    )
    ->addLineItem(
        LineItemBuilder::text()
            ->name('Note')
            ->description('Additional context for the invoice')
    )
    ->taxConditions('net')
    ->paymentConditions(
        label: '10 days - 3%',
        duration: 30,
        discountPercentage: 3.0,
        discountRange: 10
    )
    ->shippingConditions(
        date: now()->addDays(5),
        type: 'delivery'
    )
    ->title('Invoice')
    ->introduction('Introduction text for the invoice')
    ->remark('Thank you for your business!')
    ->toArray(); // Automatically validates the invoice data
```

### Error Handling

The package provides enhanced error handling with detailed exception information:

```php
use PhilHarmonie\LexOffice\Exceptions\ApiException;

try {
    $invoice = Invoice::create($data);
} catch (ApiException $e) {
    echo "Status Code: " . $e->getStatusCode();
    echo "Response: " . json_encode($e->getResponse());
    echo "Message: " . $e->getMessage();
}
```

### Advanced Usage

For advanced use cases, you can inject services directly:

```php
use PhilHarmonie\LexOffice\Services\ContactService;
use PhilHarmonie\LexOffice\Services\InvoiceService;

class MyController
{
    public function __construct(
        private ContactService $contactService,
        private InvoiceService $invoiceService
    ) {}

    public function createInvoice()
    {
        $contact = $this->contactService->find('contact-id');
        $invoice = $this->invoiceService->create($data);
    }
}
```

### Performance Features

The package includes several performance optimizations:

- **Automatic Caching**: GET requests to read-only endpoints are automatically cached
- **Rate Limiting**: Automatic throttling to stay within API limits (2 requests/second)
- **Retry Logic**: Automatic retry with exponential backoff for 5xx errors and rate limits

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

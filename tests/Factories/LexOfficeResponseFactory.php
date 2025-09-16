<?php

declare(strict_types=1);

namespace Tests\Factories;

final class LexOfficeResponseFactory
{
    public static function contact(array $overrides = []): array
    {
        return array_merge([
            'id' => 'contact-123',
            'name' => 'Test Company',
            'email' => 'test@example.com',
            'phone' => '+49 123 456789',
            'website' => 'https://example.com',
            'note' => 'Test contact',
            'addresses' => [
                'billing' => [
                    'name' => 'Test Company',
                    'supplement' => '',
                    'street' => 'Test Street 123',
                    'city' => 'Test City',
                    'zip' => '12345',
                    'countryCode' => 'DE',
                ],
            ],
            'person' => [
                'salutation' => 'Mr',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ],
            'roles' => [
                'customer' => [
                    'number' => 'CUST-001',
                ],
            ],
            'archived' => false,
            'createdDate' => '2024-01-01T12:00:00.000+01:00',
            'updatedDate' => '2024-01-01T12:00:00.000+01:00',
            'version' => 1,
        ], $overrides);
    }

    public static function invoice(array $overrides = []): array
    {
        return array_merge([
            'id' => 'invoice-123',
            'resourceUri' => 'https://api.lexware.io/v1/invoices/invoice-123',
            'voucherNumber' => 'INV-001',
            'voucherDate' => '2024-01-01T12:00:00.000+01:00',
            'dueDate' => '2024-01-31T12:00:00.000+01:00',
            'address' => [
                'contactId' => 'contact-123',
            ],
            'lineItems' => [
                [
                    'type' => 'custom',
                    'name' => 'Test Product',
                    'description' => 'Test Description',
                    'quantity' => 1,
                    'unitName' => 'piece',
                    'unitPrice' => [
                        'currency' => 'EUR',
                        'netAmount' => 100.00,
                        'taxRatePercentage' => 19.0,
                    ],
                ],
            ],
            'totalPrice' => [
                'currency' => 'EUR',
                'totalGrossAmount' => 119.00,
                'totalNetAmount' => 100.00,
                'totalTaxAmount' => 19.00,
            ],
            'taxAmount' => [
                'currency' => 'EUR',
                'taxAmount' => 19.00,
            ],
            'taxType' => 'net',
            'paymentConditions' => [
                'paymentTermLabel' => '10 days - 2%',
                'paymentTermDuration' => 30,
                'paymentDiscountConditions' => [
                    'discountPercentage' => 2.0,
                    'discountRange' => 10,
                ],
            ],
            'shippingConditions' => [
                'shippingDate' => '2024-01-05T12:00:00.000+01:00',
                'shippingType' => 'delivery',
            ],
            'title' => 'Invoice',
            'introduction' => 'Thank you for your order',
            'remark' => 'Please pay within 30 days',
            'files' => [
                'documentFileId' => 'file-123',
            ],
            'createdDate' => '2024-01-01T12:00:00.000+01:00',
            'updatedDate' => '2024-01-01T12:00:00.000+01:00',
            'version' => 1,
        ], $overrides);
    }

    public static function dunning(array $overrides = []): array
    {
        return array_merge([
            'id' => 'dunning-123',
            'resourceUri' => '/dunnings/dunning-123',
            'voucherNumber' => 'MAHN-001',
            'voucherDate' => '2024-01-01',
            'address' => [
                'contactId' => 'contact-123',
            ],
            'lineItems' => [
                [
                    'type' => 'custom',
                    'name' => 'Dunning Fee',
                    'description' => 'Late payment fee',
                    'quantity' => 1,
                    'unitName' => 'piece',
                    'unitPrice' => [
                        'currency' => 'EUR',
                        'netAmount' => 5.00,
                        'taxRatePercentage' => 19.0,
                    ],
                ],
            ],
            'totalPrice' => [
                'currency' => 'EUR',
                'totalGrossAmount' => 5.95,
                'totalNetAmount' => 5.00,
                'totalTaxAmount' => 0.95,
            ],
            'taxAmount' => [
                'currency' => 'EUR',
                'taxAmount' => 0.95,
            ],
            'taxType' => 'net',
            'paymentConditions' => [
                'paymentTermLabel' => 'Payment within 14 days',
                'paymentTermDuration' => 14,
            ],
            'shippingConditions' => [
                'shippingDate' => '2024-01-01',
                'shippingType' => 'delivery',
            ],
            'title' => 'Payment Reminder',
            'introduction' => 'Please pay your outstanding invoice',
            'remark' => 'Payment overdue',
            'files' => [
                'documentFileId' => 'file-123',
            ],
            'createdDate' => '2024-01-01T12:00:00.000+01:00',
            'updatedDate' => '2024-01-01T12:00:00.000+01:00',
            'version' => 1,
        ], $overrides);
    }

    public static function country(array $overrides = []): array
    {
        return array_merge([
            'code' => 'DE',
            'name' => 'Germany',
            'taxClassification' => 'eu',
        ], $overrides);
    }

    public static function errorResponse(int $statusCode = 400, string $message = 'Bad Request'): array
    {
        return [
            'message' => $message,
            'status' => $statusCode,
            'timestamp' => '2024-01-01T12:00:00.000+01:00',
        ];
    }

    public static function rateLimitResponse(): array
    {
        return [
            'message' => 'Rate limit exceeded',
            'status' => 429,
            'timestamp' => '2024-01-01T12:00:00.000+01:00',
        ];
    }
}

<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Builders;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

final class InvoiceBuilder
{
    /** @var array{
     *     archived: bool,
     *     totalPrice: array{currency: string},
     *     lineItems?: array<int, mixed>,
     *     address?: array<string, string>,
     *     voucherDate?: string,
     *     taxConditions?: array{taxType: string},
     *     paymentConditions?: array{
     *         paymentTermLabel: string,
     *         paymentTermDuration: int,
     *         paymentDiscountConditions?: array{
     *             discountPercentage: float,
     *             discountRange: int
     *         }
     *     },
     *     shippingConditions?: array{
     *         shippingDate: string,
     *         shippingType: string
     *     },
     *     title?: string,
     *     introduction?: string,
     *     remark?: string
     * } */
    private array $data = [
        'archived' => false,
        'totalPrice' => [
            'currency' => 'EUR',
        ],
    ];

    private ?DateTimeZone $timezone = null;

    private function __construct() {}

    public static function make(): self
    {
        return new self;
    }

    public function archived(bool $archived = true): self
    {
        $this->data['archived'] = $archived;

        return $this;
    }

    public function timezone(string|DateTimeZone|null $timezone): self
    {
        $this->timezone = $timezone instanceof DateTimeZone
            ? $timezone
            : ($timezone ? new DateTimeZone($timezone) : null);

        return $this;
    }

    public function voucherDate(DateTimeInterface $date): self
    {
        $formattedDate = $date instanceof DateTime
            ? clone $date
            : DateTime::createFromInterface($date);

        if ($this->timezone instanceof DateTimeZone) {
            $formattedDate = $formattedDate->setTimezone($this->timezone);
        }

        $this->data['voucherDate'] = $formattedDate->format('Y-m-d\TH:i:s.000P');

        return $this;
    }

    /**
     * Create an invoice for a specific contact
     */
    public function forContact(string $contactId): self
    {
        $this->data['address'] = ['contactId' => $contactId];

        return $this;
    }

    /**
     * @param  AddressBuilder|array<string, string>  $address
     */
    public function address(AddressBuilder|array $address): self
    {
        $this->data['address'] = $address instanceof AddressBuilder
            ? $address->toArray()
            : $address;

        return $this;
    }

    /**
     * @param  LineItemBuilder|array<string, mixed>  $item
     */
    public function addLineItem(LineItemBuilder|array $item): self
    {
        $this->data['lineItems'][] = $item instanceof LineItemBuilder
            ? $item->toArray()
            : $item;

        return $this;
    }

    public function taxConditions(string $taxType = 'net'): self
    {
        if (! in_array($taxType, ['net', 'gross', 'vatfree'], true)) {
            throw new InvalidArgumentException('Invalid tax type.');
        }

        $this->data['taxConditions'] = ['taxType' => $taxType];

        return $this;
    }

    public function paymentConditions(
        string $label,
        int $duration,
        ?float $discountPercentage = null,
        ?int $discountRange = null
    ): self {
        $conditions = [
            'paymentTermLabel' => $label,
            'paymentTermDuration' => $duration,
        ];

        if ($discountPercentage !== null && $discountRange !== null) {
            $conditions['paymentDiscountConditions'] = [
                'discountPercentage' => $discountPercentage,
                'discountRange' => $discountRange,
            ];
        }

        $this->data['paymentConditions'] = $conditions;

        return $this;
    }

    public function shippingConditions(
        DateTimeInterface $date,
        string $type = 'delivery'
    ): self {
        if (! in_array($type, ['delivery', 'service'], true)) {
            throw new InvalidArgumentException('Invalid shipping type.');
        }

        $this->data['shippingConditions'] = [
            'shippingDate' => $this->formatDateTime($date),
            'shippingType' => $type,
        ];

        return $this;
    }

    public function title(string $title): self
    {
        $this->data['title'] = $title;

        return $this;
    }

    public function introduction(string $text): self
    {
        $this->data['introduction'] = $text;

        return $this;
    }

    public function remark(string $text): self
    {
        $this->data['remark'] = $text;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->data;
    }

    private function formatDateTime(DateTimeInterface $date): string
    {
        $formattedDate = $date instanceof DateTime
            ? clone $date
            : DateTime::createFromInterface($date);

        if ($this->timezone instanceof DateTimeZone) {
            $formattedDate = $formattedDate->setTimezone($this->timezone);
        }

        return $formattedDate->format('Y-m-d\TH:i:s.000P');
    }
}

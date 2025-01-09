<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Builders;

use InvalidArgumentException;

final class LineItemBuilder
{
    /** @var array<string, mixed> */
    private array $data;

    private function __construct(string $type)
    {
        if (! in_array($type, ['custom', 'text'], true)) {
            throw new InvalidArgumentException('Invalid line item type.');
        }

        $this->data['type'] = $type;
    }

    public static function custom(): self
    {
        return new self('custom');
    }

    public static function text(): self
    {
        return new self('text');
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function description(?string $description): self
    {
        if ($description !== null) {
            $this->data['description'] = $description;
        }

        return $this;
    }

    public function quantity(float|int $quantity): self
    {
        if ($this->data['type'] !== 'custom') {
            throw new InvalidArgumentException('Quantity can only be set for custom items.');
        }

        $this->data['quantity'] = $quantity;

        return $this;
    }

    public function unitName(string $name): self
    {
        if ($this->data['type'] !== 'custom') {
            throw new InvalidArgumentException('Unit name can only be set for custom items.');
        }

        $this->data['unitName'] = $name;

        return $this;
    }

    public function unitPrice(
        string $currency,
        float $netAmount,
        float $taxRatePercentage
    ): self {
        if ($this->data['type'] !== 'custom') {
            throw new InvalidArgumentException('Unit price can only be set for custom items.');
        }

        $this->data['unitPrice'] = [
            'currency' => $currency,
            'netAmount' => $netAmount,
            'taxRatePercentage' => $taxRatePercentage,
        ];

        return $this;
    }

    public function discountPercentage(float $percentage): self
    {
        if ($this->data['type'] !== 'custom') {
            throw new InvalidArgumentException('Discount can only be set for custom items.');
        }

        $this->data['discountPercentage'] = $percentage;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->data;
    }
}

<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Builders;

final class AddressBuilder
{
    /** @var array<string, string> */
    private array $data = [];

    private function __construct() {}

    public static function make(): self
    {
        return new self;
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function street(string $street): self
    {
        $this->data['street'] = $street;

        return $this;
    }

    public function city(string $city): self
    {
        $this->data['city'] = $city;

        return $this;
    }

    public function zip(string $zip): self
    {
        $this->data['zip'] = $zip;

        return $this;
    }

    public function countryCode(string $code): self
    {
        $this->data['countryCode'] = $code;

        return $this;
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return $this->data;
    }
}

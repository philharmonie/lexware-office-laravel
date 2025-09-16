<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\DTOs;

final readonly class CountryDto
{
    public function __construct(
        public mixed $code,
        public mixed $name,
        public mixed $taxClassification
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            taxClassification: $data['taxClassification'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'taxClassification' => $this->taxClassification,
        ];
    }
}

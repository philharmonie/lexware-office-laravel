<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\DTOs;

final readonly class ContactDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $email,
        public ?string $phone,
        public ?string $website,
        public ?string $note,
        public array $addresses,
        public array $person,
        public array $roles,
        public ?string $archived,
        public string $createdDate,
        public string $updatedDate,
        public ?string $version
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            website: $data['website'] ?? null,
            note: $data['note'] ?? null,
            addresses: $data['addresses'] ?? [],
            person: $data['person'] ?? [],
            roles: $data['roles'] ?? [],
            archived: $data['archived'] ?? null,
            createdDate: $data['createdDate'],
            updatedDate: $data['updatedDate'],
            version: $data['version'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'note' => $this->note,
            'addresses' => $this->addresses,
            'person' => $this->person,
            'roles' => $this->roles,
            'archived' => $this->archived,
            'createdDate' => $this->createdDate,
            'updatedDate' => $this->updatedDate,
            'version' => $this->version,
        ];
    }
}

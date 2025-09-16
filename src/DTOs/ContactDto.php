<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\DTOs;

final readonly class ContactDto
{
    public function __construct(
        public mixed $id,
        public mixed $name,
        public mixed $email,
        public mixed $phone,
        public mixed $website,
        public mixed $note,
        public mixed $addresses,
        public mixed $person,
        public mixed $roles,
        public mixed $archived,
        public mixed $createdDate,
        public mixed $updatedDate,
        public mixed $version
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
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

    /**
     * @return array<string, mixed>
     */
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

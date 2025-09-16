<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\DTOs;

final readonly class InvoiceDto
{
    public function __construct(
        public string $id,
        public string $resourceUri,
        public string $voucherNumber,
        public string $voucherDate,
        public ?string $dueDate,
        public ?string $address,
        public ?string $lineItems,
        public ?string $totalPrice,
        public ?string $taxAmount,
        public ?string $taxType,
        public ?string $paymentConditions,
        public ?string $shippingConditions,
        public ?string $title,
        public ?string $introduction,
        public ?string $remark,
        public ?string $files,
        public string $createdDate,
        public string $updatedDate,
        public ?string $version
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            resourceUri: $data['resourceUri'],
            voucherNumber: $data['voucherNumber'],
            voucherDate: $data['voucherDate'],
            dueDate: $data['dueDate'] ?? null,
            address: $data['address'] ?? null,
            lineItems: $data['lineItems'] ?? null,
            totalPrice: $data['totalPrice'] ?? null,
            taxAmount: $data['taxAmount'] ?? null,
            taxType: $data['taxType'] ?? null,
            paymentConditions: $data['paymentConditions'] ?? null,
            shippingConditions: $data['shippingConditions'] ?? null,
            title: $data['title'] ?? null,
            introduction: $data['introduction'] ?? null,
            remark: $data['remark'] ?? null,
            files: $data['files'] ?? null,
            createdDate: $data['createdDate'],
            updatedDate: $data['updatedDate'],
            version: $data['version'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'resourceUri' => $this->resourceUri,
            'voucherNumber' => $this->voucherNumber,
            'voucherDate' => $this->voucherDate,
            'dueDate' => $this->dueDate,
            'address' => $this->address,
            'lineItems' => $this->lineItems,
            'totalPrice' => $this->totalPrice,
            'taxAmount' => $this->taxAmount,
            'taxType' => $this->taxType,
            'paymentConditions' => $this->paymentConditions,
            'shippingConditions' => $this->shippingConditions,
            'title' => $this->title,
            'introduction' => $this->introduction,
            'remark' => $this->remark,
            'files' => $this->files,
            'createdDate' => $this->createdDate,
            'updatedDate' => $this->updatedDate,
            'version' => $this->version,
        ];
    }
}

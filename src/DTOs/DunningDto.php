<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\DTOs;

final readonly class DunningDto
{
    public function __construct(
        public mixed $id,
        public mixed $resourceUri,
        public mixed $voucherNumber,
        public mixed $voucherDate,
        public mixed $address,
        public mixed $lineItems,
        public mixed $totalPrice,
        public mixed $taxAmount,
        public mixed $taxType,
        public mixed $paymentConditions,
        public mixed $shippingConditions,
        public mixed $title,
        public mixed $introduction,
        public mixed $remark,
        public mixed $files,
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
            resourceUri: $data['resourceUri'],
            voucherNumber: $data['voucherNumber'],
            voucherDate: $data['voucherDate'],
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'resourceUri' => $this->resourceUri,
            'voucherNumber' => $this->voucherNumber,
            'voucherDate' => $this->voucherDate,
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

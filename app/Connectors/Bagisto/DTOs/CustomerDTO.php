<?php

namespace App\Connectors\Bagisto\DTOs;

class CustomerDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $companyName = null,
        public ?array $billingAddress = [],
        public ?array $shippingAddress = [],
        public ?string $customerGroup = null,
        public ?bool $isActive = true,
        public ?string $externalId = null,
        public ?array $metadata = []
    ) {}

    /**
     * Create DTO from array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            companyName: $data['company_name'] ?? null,
            billingAddress: $data['billing_address'] ?? [],
            shippingAddress: $data['shipping_address'] ?? [],
            customerGroup: $data['customer_group'] ?? null,
            isActive: $data['is_active'] ?? true,
            externalId: $data['external_id'] ?? null,
            metadata: $data['metadata'] ?? []
        );
    }

    /**
     * Convert DTO to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'company_name' => $this->companyName,
            'billing_address' => $this->billingAddress,
            'shipping_address' => $this->shippingAddress,
            'customer_group' => $this->customerGroup,
            'is_active' => $this->isActive,
            'external_id' => $this->externalId,
            'metadata' => $this->metadata,
        ];
    }
}

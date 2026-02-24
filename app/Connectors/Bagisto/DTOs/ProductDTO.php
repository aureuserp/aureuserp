<?php

namespace App\Connectors\Bagisto\DTOs;

class ProductDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $sku = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?float $price = null,
        public ?float $cost = null,
        public ?int $quantity = null,
        public ?string $type = 'simple',
        public ?int $categoryId = null,
        public ?array $images = [],
        public ?array $attributes = [],
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
            sku: $data['sku'] ?? null,
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            price: $data['price'] ?? null,
            cost: $data['cost'] ?? null,
            quantity: $data['quantity'] ?? null,
            type: $data['type'] ?? 'simple',
            categoryId: $data['category_id'] ?? null,
            images: $data['images'] ?? [],
            attributes: $data['attributes'] ?? [],
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
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'cost' => $this->cost,
            'quantity' => $this->quantity,
            'type' => $this->type,
            'category_id' => $this->categoryId,
            'images' => $this->images,
            'attributes' => $this->attributes,
            'is_active' => $this->isActive,
            'external_id' => $this->externalId,
            'metadata' => $this->metadata,
        ];
    }
}

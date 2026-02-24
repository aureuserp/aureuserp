<?php

namespace App\Connectors\Bagisto\DTOs;

class InventoryDTO
{
    public function __construct(
        public ?int $productId = null,
        public ?string $sku = null,
        public ?int $quantity = null,
        public ?int $reservedQuantity = null,
        public ?int $availableQuantity = null,
        public ?string $warehouseCode = null,
        public ?string $location = null,
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
            productId: $data['product_id'] ?? null,
            sku: $data['sku'] ?? null,
            quantity: $data['quantity'] ?? null,
            reservedQuantity: $data['reserved_quantity'] ?? null,
            availableQuantity: $data['available_quantity'] ?? null,
            warehouseCode: $data['warehouse_code'] ?? null,
            location: $data['location'] ?? null,
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
            'product_id' => $this->productId,
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'reserved_quantity' => $this->reservedQuantity,
            'available_quantity' => $this->availableQuantity,
            'warehouse_code' => $this->warehouseCode,
            'location' => $this->location,
            'external_id' => $this->externalId,
            'metadata' => $this->metadata,
        ];
    }
}

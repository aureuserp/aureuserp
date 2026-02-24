<?php

namespace App\Connectors\Bagisto\DTOs;

class OrderDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $orderNumber = null,
        public ?int $customerId = null,
        public ?array $items = [],
        public ?float $subtotal = null,
        public ?float $tax = null,
        public ?float $shipping = null,
        public ?float $discount = null,
        public ?float $total = null,
        public ?string $status = null,
        public ?string $paymentMethod = null,
        public ?string $shippingMethod = null,
        public ?array $billingAddress = [],
        public ?array $shippingAddress = [],
        public ?string $notes = null,
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
            orderNumber: $data['order_number'] ?? null,
            customerId: $data['customer_id'] ?? null,
            items: $data['items'] ?? [],
            subtotal: $data['subtotal'] ?? null,
            tax: $data['tax'] ?? null,
            shipping: $data['shipping'] ?? null,
            discount: $data['discount'] ?? null,
            total: $data['total'] ?? null,
            status: $data['status'] ?? null,
            paymentMethod: $data['payment_method'] ?? null,
            shippingMethod: $data['shipping_method'] ?? null,
            billingAddress: $data['billing_address'] ?? [],
            shippingAddress: $data['shipping_address'] ?? [],
            notes: $data['notes'] ?? null,
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
            'order_number' => $this->orderNumber,
            'customer_id' => $this->customerId,
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'shipping' => $this->shipping,
            'discount' => $this->discount,
            'total' => $this->total,
            'status' => $this->status,
            'payment_method' => $this->paymentMethod,
            'shipping_method' => $this->shippingMethod,
            'billing_address' => $this->billingAddress,
            'shipping_address' => $this->shippingAddress,
            'notes' => $this->notes,
            'external_id' => $this->externalId,
            'metadata' => $this->metadata,
        ];
    }
}

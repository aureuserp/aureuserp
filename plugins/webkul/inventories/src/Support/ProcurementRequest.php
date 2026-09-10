<?php

namespace Webkul\Inventory\Support;

use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class ProcurementRequest
{
    public function __construct(
        public readonly Product $product,
        public readonly float $quantity,
        public readonly UOM $uom,
        public readonly Location $location,
        public readonly string $name,
        public readonly ?string $origin,
        public readonly ?Company $company,
        public readonly ProcurementOptions $options,
    ) {}

    public function withQuantity(float $quantity): self
    {
        return new self(
            product: $this->product,
            quantity: $quantity,
            uom: $this->uom,
            location: $this->location,
            name: $this->name,
            origin: $this->origin,
            company: $this->company,
            options: $this->options,
        );
    }

    public function effectiveCompany(): ?Company
    {
        return $this->options->targetCompany() ?? $this->company ?? $this->location->company;
    }
}

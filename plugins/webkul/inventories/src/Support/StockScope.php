<?php

namespace Webkul\Inventory\Support;

use Carbon\Carbon;

class StockScope
{
    protected array $locationIds = [];

    protected array $warehouseIds = [];

    protected array $companyIds = [];

    protected ?int $lotId = null;

    protected ?int $packageId = null;

    protected ?Carbon $from = null;

    protected ?Carbon $until = null;

    protected bool $strict = false;

    public static function make(): static
    {
        return new static;
    }

    public function forLocations(int|array|null $locations): static
    {
        $this->locationIds = $this->toIds($locations);

        return $this;
    }

    public function forWarehouses(int|array|null $warehouses): static
    {
        $this->warehouseIds = $this->toIds($warehouses);

        return $this;
    }

    public function forCompanies(int|array|null $companies): static
    {
        $this->companyIds = $this->toIds($companies);

        return $this;
    }

    public function forLot(?int $lotId): static
    {
        $this->lotId = $lotId;

        return $this;
    }

    public function forPackage(?int $packageId): static
    {
        $this->packageId = $packageId;

        return $this;
    }

    public function from(?Carbon $from): static
    {
        $this->from = $from;

        return $this;
    }

    public function asOf(?Carbon $until): static
    {
        $this->until = $until;

        return $this;
    }

    public function strictly(bool $strict = true): static
    {
        $this->strict = $strict;

        return $this;
    }

    public function locationIds(): array
    {
        return $this->locationIds;
    }

    public function warehouseIds(): array
    {
        return $this->warehouseIds;
    }

    public function companyIds(): array
    {
        return $this->companyIds;
    }

    public function lotId(): ?int
    {
        return $this->lotId;
    }

    public function packageId(): ?int
    {
        return $this->packageId;
    }

    public function fromDate(): ?Carbon
    {
        return $this->from;
    }

    public function untilDate(): ?Carbon
    {
        return $this->until;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function isBackdated(): bool
    {
        return $this->until !== null && $this->until->lt(now());
    }

    protected function toIds(int|array|null $value): array
    {
        return collect(is_array($value) ? $value : [$value])
            ->reject(fn ($id) => $id === null || $id === '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }
}

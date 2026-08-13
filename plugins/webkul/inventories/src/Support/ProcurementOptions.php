<?php

namespace Webkul\Inventory\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OrderPoint;
use Webkul\Inventory\Models\Packaging;
use Webkul\Inventory\Models\ProcurementGroup;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;

class ProcurementOptions
{
    protected ?Carbon $plannedAt = null;

    protected ?Carbon $orderedAt = null;

    protected ?Carbon $deadline = null;

    protected Collection $moveDestinations;

    protected Collection $routes;

    protected ?ProcurementGroup $group = null;

    protected ?Warehouse $warehouse = null;

    protected ?OrderPoint $orderPoint = null;

    protected ?Packaging $packaging = null;

    protected ?Partner $partner = null;

    protected ?Company $company = null;

    protected ?Location $finalLocation = null;

    protected string $priority = '0';

    protected ?int $saleOrderLineId = null;

    protected ?int $purchaseOrderLineId = null;

    protected ?int $workOrderId = null;

    protected ?int $bomLineId = null;

    public function __construct()
    {
        $this->moveDestinations = collect();

        $this->routes = collect();
    }

    public static function make(): static
    {
        return new static;
    }

    public function plannedAt(?Carbon $plannedAt): static
    {
        $this->plannedAt = $plannedAt;

        return $this;
    }

    public function orderedAt(?Carbon $orderedAt): static
    {
        $this->orderedAt = $orderedAt;

        return $this;
    }

    public function deadline(?Carbon $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function moveDestinations(?Collection $moves): static
    {
        $this->moveDestinations = $moves ?? collect();

        return $this;
    }

    public function routes(?Collection $routes): static
    {
        $this->routes = $routes ?? collect();

        return $this;
    }

    public function group(?ProcurementGroup $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function warehouse(?Warehouse $warehouse): static
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function orderPoint(?OrderPoint $orderPoint): static
    {
        $this->orderPoint = $orderPoint;

        return $this;
    }

    public function packaging(?Packaging $packaging): static
    {
        $this->packaging = $packaging;

        return $this;
    }

    public function partner(?Partner $partner): static
    {
        $this->partner = $partner;

        return $this;
    }

    public function company(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function finalLocation(?Location $location): static
    {
        $this->finalLocation = $location;

        return $this;
    }

    public function priority(string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function linkSaleOrderLine(?int $id): static
    {
        $this->saleOrderLineId = $id;

        return $this;
    }

    public function linkPurchaseOrderLine(?int $id): static
    {
        $this->purchaseOrderLineId = $id;

        return $this;
    }

    public function linkWorkOrder(?int $id): static
    {
        $this->workOrderId = $id;

        return $this;
    }

    public function linkBomLine(?int $id): static
    {
        $this->bomLineId = $id;

        return $this;
    }

    public function plannedDate(): ?Carbon
    {
        return $this->plannedAt;
    }

    public function orderedDate(): ?Carbon
    {
        return $this->orderedAt;
    }

    public function deadlineDate(): ?Carbon
    {
        return $this->deadline;
    }

    public function destinationMoves(): Collection
    {
        return $this->moveDestinations;
    }

    public function routeSet(): Collection
    {
        return $this->routes;
    }

    public function procurementGroup(): ?ProcurementGroup
    {
        return $this->group;
    }

    public function targetWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function reorderRule(): ?OrderPoint
    {
        return $this->orderPoint;
    }

    public function productPackaging(): ?Packaging
    {
        return $this->packaging;
    }

    public function targetPartner(): ?Partner
    {
        return $this->partner;
    }

    public function targetCompany(): ?Company
    {
        return $this->company;
    }

    public function destinationLocation(): ?Location
    {
        return $this->finalLocation;
    }

    public function priorityLevel(): string
    {
        return $this->priority;
    }

    public function saleOrderLineId(): ?int
    {
        return $this->saleOrderLineId;
    }

    public function purchaseOrderLineId(): ?int
    {
        return $this->purchaseOrderLineId;
    }

    public function workOrderId(): ?int
    {
        return $this->workOrderId;
    }

    public function bomLineId(): ?int
    {
        return $this->bomLineId;
    }
}

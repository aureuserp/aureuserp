<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Collection;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Manufacturing\Enums\ManufacturingOrderReservationState;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class OverviewManufacturingOrder extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ManufacturingOrderResource::class;

    protected string $view = 'manufacturing::filament.clusters.operations.resources.manufacturing-order.pages.overview-manufacturing-order';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->getRecord()->load([
            'product.uom',
            'product.uomPO',
            'product.quantities',
            'uom',
            'billOfMaterial',
            'rawMaterialMoves.uom',
            'rawMaterialMoves.product.uom',
            'rawMaterialMoves.product.uomPO',
            'rawMaterialMoves.product.quantities',
            'rawMaterialMoves.lines',
            'workOrders.product.uom',
            'workOrders.workCenter',
            'workOrders.operation',
        ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.title');
    }

    public function getTitle(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.title');
    }

    public function getOverviewProductName(): string
    {
        return $this->getRecord()->product?->name ?? '—';
    }

    public function getOverviewUomName(): string
    {
        return $this->getRecord()->uom?->name ?? $this->getRecord()->product?->uom?->name ?? '—';
    }

    public function getProductAvailableQuantity(): float
    {
        return (float) ($this->getRecord()->product?->free_qty ?? 0);
    }

    public function getProductOnHandQuantity(): float
    {
        return (float) ($this->getRecord()->product?->available_qty ?? 0);
    }

    public function getReceiptDateLabel(): string
    {
        return ($this->getRecord()->deadline_at ?? now())->format('m/d/Y');
    }

    public function getOrderStatusLabel(): string
    {
        $record = $this->getRecord();

        if ($record->reservation_state === ManufacturingOrderReservationState::ASSIGNED) {
            return __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.status.ready');
        }

        return $record->state?->getLabel() ?? '—';
    }

    public function getOrderStatusColor(): string
    {
        $record = $this->getRecord();

        if ($record->reservation_state === ManufacturingOrderReservationState::ASSIGNED) {
            return 'success';
        }

        return $record->state?->getColor() ?? 'gray';
    }

    public function getComponentRows(): Collection
    {
        return $this->getRecord()->rawMaterialMoves
            ->sortBy('id')
            ->values();
    }

    public function getWorkOrderRows(): Collection
    {
        return $this->getRecord()->workOrders
            ->sortBy('id')
            ->values();
    }

    public function getReservedQuantity(Move $move): float
    {
        if (! $move->product_id) {
            return 0.0;
        }

        $query = ProductQuantity::query()
            ->where('product_id', $move->product_id);

        if ($move->source_location_id) {
            $query->where('location_id', $move->source_location_id);
        }

        return (float) $query->sum('reserved_quantity');
    }

    public function getComponentStatusLabel(Move $move): string
    {
        return $move->product && $move->product->free_qty >= $move->product_uom_qty
            ? __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.status.available')
            : __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.status.waiting');
    }

    public function getComponentStatusColor(Move $move): string
    {
        return $move->product && $move->product->free_qty >= $move->product_uom_qty
            ? 'success'
            : 'warning';
    }

    public function getComponentReceiptLabel(Move $move): string
    {
        if ($move->product && $move->product->free_qty >= $move->product_uom_qty) {
            return __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.status.available');
        }

        return __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.receipt.expected', [
            'date' => $this->getReceiptDateLabel(),
        ]);
    }

    public function getComponentUnitCost(Move $move): float
    {
        return round($move->product?->getCostForQuantity(1, $move->uom) ?? 0, 2);
    }

    public function getComponentTotalCost(Move $move): float
    {
        return round(
            $move->product?->getCostForQuantity((float) $move->product_uom_qty, $move->uom) ?? 0,
            2,
        );
    }

    public function getComponentRealCost(Move $move): float
    {
        if ($this->getRecord()->state !== ManufacturingOrderState::DONE) {
            return 0.0;
        }

        $actualQuantity = (float) ($move->quantity ?: $move->product_uom_qty);

        return round($move->product?->getCostForQuantity($actualQuantity, $move->uom) ?? 0, 2);
    }

    public function getWorkOrderUnitCost(WorkOrder $workOrder): float
    {
        $costPerHour = (float) ($workOrder->costs_per_hour ?: $workOrder->workCenter?->costs_per_hour ?: 0);

        return round($costPerHour / 60, 2);
    }

    public function getWorkOrderTotalCost(WorkOrder $workOrder): float
    {
        $costPerHour = (float) ($workOrder->costs_per_hour ?: $workOrder->workCenter?->costs_per_hour ?: 0);

        return round(((float) $workOrder->expected_duration / 60) * $costPerHour, 2);
    }

    public function getWorkOrderRealCost(WorkOrder $workOrder): float
    {
        if ($this->getRecord()->state !== ManufacturingOrderState::DONE) {
            return 0.0;
        }

        $costPerHour = (float) ($workOrder->costs_per_hour ?: $workOrder->workCenter?->costs_per_hour ?: 0);

        return round(((float) $workOrder->duration / 60) * $costPerHour, 2);
    }

    public function getTotalOperationDuration(): float
    {
        return (float) $this->getWorkOrderRows()->sum('expected_duration');
    }

    public function getTotalOperationDurationLabel(): string
    {
        return format_float_time($this->getTotalOperationDuration(), 'minutes');
    }

    public function getTotalOperationCost(): float
    {
        return round(
            $this->getWorkOrderRows()->sum(fn (WorkOrder $workOrder): float => $this->getWorkOrderTotalCost($workOrder)),
            2,
        );
    }

    public function getTotalComponentCost(): float
    {
        return round(
            $this->getComponentRows()->sum(fn (Move $move): float => $this->getComponentTotalCost($move)),
            2,
        );
    }

    public function getTotalRealComponentCost(): float
    {
        return round(
            $this->getComponentRows()->sum(fn (Move $move): float => $this->getComponentRealCost($move)),
            2,
        );
    }

    public function getUnitCost(): float
    {
        $quantity = (float) ($this->getRecord()->quantity ?: 0);

        if ($quantity <= 0) {
            return 0;
        }

        return round($this->getTotalMoCost() / $quantity, 2);
    }

    public function getRealUnitCost(): float
    {
        $quantity = (float) ($this->getRecord()->quantity ?: 0);

        if ($quantity <= 0) {
            return 0;
        }

        return round($this->getTotalRealCost() / $quantity, 2);
    }

    public function getTotalMoCost(): float
    {
        return round($this->getTotalComponentCost() + $this->getTotalOperationCost(), 2);
    }

    public function getTotalRealOperationCost(): float
    {
        return round(
            $this->getWorkOrderRows()->sum(fn (WorkOrder $workOrder): float => $this->getWorkOrderRealCost($workOrder)),
            2,
        );
    }

    public function getTotalRealCost(): float
    {
        return round($this->getTotalRealComponentCost() + $this->getTotalRealOperationCost(), 2);
    }

    public function getTotalBomCost(): float
    {
        return $this->getTotalMoCost();
    }

    public function getParentProductCost(): float
    {
        return round(
            $this->getRecord()->product?->getCostForQuantity(
                (float) $this->getRecord()->quantity,
                $this->getRecord()->uom,
            ) ?? 0,
            2,
        );
    }
}

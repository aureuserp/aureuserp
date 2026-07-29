<?php

namespace Webkul\Manufacturing\Observers;

use Webkul\Inventory\Models\Move as InventoryMove;
use Webkul\Manufacturing\Enums\WorkOrderProductionAvailability;
use Webkul\Manufacturing\Models\Move as ManufacturingMove;
use Webkul\PluginManager\Package;

class MoveObserver
{
    public function updated(InventoryMove $move): void
    {
        if (! Package::isPluginInstalled('manufacturing')) {
            return;
        }

        $move = ManufacturingMove::find($move->id);

        if (! $move?->raw_material_order_id) {
            return;
        }

        $rawMaterialOrder = $move->rawMaterialOrder;

        $rawMaterialOrder->computeReservationState();

        $rawMaterialOrder->saveQuietly();

        $rawMaterialOrder->workOrders->each(function ($workOrder) use ($rawMaterialOrder) {
            $workOrder->production_availability = $rawMaterialOrder->reservation_state ? WorkOrderProductionAvailability::from($rawMaterialOrder->reservation_state->value) : null;

            $workOrder->save();
        });
    }

    public function deleted(InventoryMove $move): void {}

    public function restored(InventoryMove $move): void {}
}

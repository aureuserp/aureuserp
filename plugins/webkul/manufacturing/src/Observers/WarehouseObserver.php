<?php

namespace Webkul\Manufacturing\Observers;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Webkul\Inventory\Models\Warehouse as InventoryWarehouse;
use Webkul\Manufacturing\Models\Warehouse as ManufacturingWarehouse;
use Webkul\PluginManager\Package;

class WarehouseObserver implements ShouldHandleEventsAfterCommit
{
    public function created(InventoryWarehouse $warehouse): void
    {
        $warehouse = static::resolveManufacturingWarehouse($warehouse);

        if (! $warehouse) {
            return;
        }

        $warehouse->handleManufacturingWarehouseCreation();

        $warehouse->finalizeManufacturingWarehouseCreation();
    }

    public function updated(InventoryWarehouse $warehouse): void
    {
        $warehouse = static::resolveManufacturingWarehouse($warehouse);

        if (! $warehouse) {
            return;
        }

        $warehouse->syncManufacturingWarehouseConfiguration();
    }

    protected static function resolveManufacturingWarehouse(InventoryWarehouse $warehouse): ?ManufacturingWarehouse
    {
        if (! Package::isPluginInstalled('manufacturing')) {
            return null;
        }

        return ManufacturingWarehouse::find($warehouse->id);
    }

    public function deleted(InventoryWarehouse $warehouse): void {}

    public function restored(InventoryWarehouse $warehouse): void {}
}

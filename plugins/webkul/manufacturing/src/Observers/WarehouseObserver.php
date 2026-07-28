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
        if (! Package::isPluginInstalled('manufacturing')) {
            return;
        }

        $warehouse = ManufacturingWarehouse::find($warehouse->id);

        $warehouse->handleManufacturingWarehouseCreation();

        $warehouse->finalizeManufacturingWarehouseCreation();
    }

    public function updated(InventoryWarehouse $warehouse): void
    {
        $warehouse = ManufacturingWarehouse::find($warehouse->id);

        $warehouse->syncManufacturingWarehouseConfiguration();
    }

    public function deleted(InventoryWarehouse $warehouse): void {}

    public function restored(InventoryWarehouse $warehouse): void {}
}

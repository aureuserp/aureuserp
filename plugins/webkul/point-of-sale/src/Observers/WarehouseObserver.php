<?php

namespace Webkul\PointOfSale\Observers;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Webkul\Inventory\Models\Warehouse as InventoryWarehouse;
use Webkul\PluginManager\Package;
use Webkul\PointOfSale\Models\Warehouse as PointOfSaleWarehouse;

class WarehouseObserver implements ShouldHandleEventsAfterCommit
{
    public function created(InventoryWarehouse $warehouse): void
    {
        $warehouse = static::resolvePointOfSaleWarehouse($warehouse);

        if (! $warehouse) {
            return;
        }

        $warehouse->handlePosWarehouseCreation();

        $warehouse->finalizePosWarehouseCreation();
    }

    public function updated(InventoryWarehouse $warehouse): void
    {
        $warehouse = static::resolvePointOfSaleWarehouse($warehouse);

        if (! $warehouse) {
            return;
        }

        $warehouse->syncPosWarehouseConfiguration();
    }

    public function deleted(InventoryWarehouse $warehouse): void {}

    public function restored(InventoryWarehouse $warehouse): void {}

    protected static function resolvePointOfSaleWarehouse(InventoryWarehouse $warehouse): ?PointOfSaleWarehouse
    {
        if (! Package::isPluginInstalled('point-of-sale')) {
            return null;
        }

        return PointOfSaleWarehouse::find($warehouse->id);
    }
}

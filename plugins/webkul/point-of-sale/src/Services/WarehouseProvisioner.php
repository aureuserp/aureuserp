<?php

namespace Webkul\PointOfSale\Services;

use Webkul\Inventory\Models\Warehouse as InventoryWarehouse;
use Webkul\PointOfSale\Models\Warehouse;

class WarehouseProvisioner
{
    public function provision(InventoryWarehouse $warehouse): ?Warehouse
    {
        $warehouse = $this->resolve($warehouse);

        if (! $warehouse) {
            return null;
        }

        $warehouse->handlePosWarehouseCreation();

        $warehouse->finalizePosWarehouseCreation();

        return $warehouse;
    }

    public function sync(InventoryWarehouse $warehouse): ?Warehouse
    {
        $warehouse = $this->resolve($warehouse);

        if (! $warehouse) {
            return null;
        }

        $warehouse->syncPosWarehouseConfiguration();

        return $warehouse;
    }

    public function provisionAll(): void
    {
        Warehouse::query()->each(function (Warehouse $warehouse): void {
            $warehouse->handlePosWarehouseCreation();

            $warehouse->finalizePosWarehouseCreation();
        });
    }

    protected function resolve(InventoryWarehouse $warehouse): ?Warehouse
    {
        return Warehouse::find($warehouse->id);
    }
}

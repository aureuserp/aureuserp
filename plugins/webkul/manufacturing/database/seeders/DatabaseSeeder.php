<?php

namespace Webkul\Manufacturing\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Manufacturing\Models\Warehouse;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WorkCenterLossTypeSeeder::class,
            WorkCenterProductivityLossSeeder::class,
        ]);

        $warehouses = Warehouse::all();

        foreach ($warehouses as $warehouse) {
            $warehouse->handleManufacturingWarehouseCreation();

            $warehouse->finalizeManufacturingWarehouseCreation();
        }
    }
}

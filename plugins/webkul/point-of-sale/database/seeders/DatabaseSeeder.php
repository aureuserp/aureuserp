<?php

namespace Webkul\PointOfSale\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\PointOfSale\Models\Warehouse;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();

        foreach ($warehouses as $warehouse) {
            $warehouse->handlePosWarehouseCreation();

            $warehouse->finalizePosWarehouseCreation();
        }

        $this->call([
            BillSeeder::class,
            NoteSeeder::class,
            ServiceProductSeeder::class,
        ]);
    }
}

<?php

namespace Webkul\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            RouteSeeder::class,
            OperationTypeSeeder::class,
            RuleSeeder::class,
            WarehouseSeeder::class,
            SequenceSeeder::class,
        ]);
    }
}

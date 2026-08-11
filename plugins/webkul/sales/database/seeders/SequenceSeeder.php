<?php

namespace Webkul\Sale\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Sale\Models\Order;
use Webkul\Support\Services\SequenceService;

class SequenceSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        SequenceService::ensure('sales.order', null, [
            'name'         => 'Sales Order',
            'prefix'       => 'SO/',
            'initial_from' => Order::withoutGlobalScopes(),
        ]);
    }
}

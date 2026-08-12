<?php

namespace Webkul\Purchase\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Purchase\Models\Order;
use Webkul\Support\Services\SequenceService;

class SequenceSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        SequenceService::ensure('purchases.order', null, [
            'name'         => 'Purchase Order',
            'prefix'       => 'PO/',
            'initial_from' => Order::withoutGlobalScopes(),
        ]);
    }
}

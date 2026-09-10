<?php

namespace Webkul\Manufacturing\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Manufacturing\Models\Order;
use Webkul\Support\Services\SequenceService;

class SequenceSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        SequenceService::ensure('manufacturing.order', null, [
            'name'         => 'Manufacturing Order',
            'prefix'       => 'MO/',
            'initial_from' => Order::withoutGlobalScopes(),
        ]);
    }
}

<?php

namespace Webkul\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Scrap;
use Webkul\Support\Services\SequenceService;

class SequenceSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        SequenceService::ensure('inventories.scrap', null, [
            'name'         => 'Scrap',
            'prefix'       => 'SP/',
            'initial_from' => Scrap::withoutGlobalScopes(),
        ]);

        OperationType::withTrashed()->get()->each(
            fn (OperationType $operationType) => $operationType->ensureSequence()
        );
    }
}

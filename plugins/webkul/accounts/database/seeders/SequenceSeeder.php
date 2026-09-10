<?php

namespace Webkul\Account\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Account\Models\Journal;

class SequenceSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Journal::all()->each(
            fn (Journal $journal) => $journal->ensureSequences()
        );
    }
}

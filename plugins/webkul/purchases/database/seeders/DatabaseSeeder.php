<?php

namespace Webkul\Purchase\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SequenceSeeder::class,
        ]);
    }
}

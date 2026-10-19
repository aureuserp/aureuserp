<?php

namespace Webkul\Crm\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ActivityTypeSeeder::class,
            LostReasonSeeder::class,
            StageSeeder::class,
        ]);
    }
}

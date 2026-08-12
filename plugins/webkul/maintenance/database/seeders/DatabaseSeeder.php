<?php

namespace Webkul\Maintenance\Database\Seeders;

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
            StageSeeder::class,
            TeamSeeder::class,
        ]);
    }
}

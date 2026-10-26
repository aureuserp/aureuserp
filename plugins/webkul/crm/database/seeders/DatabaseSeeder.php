<?php

namespace Webkul\Crm\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ActivityTypeSeeder::class,
            LostReasonSeeder::class,
            StageSeeder::class,
            TagSeeder::class,
            RecurringPlanSeeder::class,
            PartnerLevelSeeder::class,
        ]);
    }
}

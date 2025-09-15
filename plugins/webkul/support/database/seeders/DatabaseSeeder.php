<?php

namespace Webkul\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::transaction(function () {
            $this->call([
                CurrencySeeder::class,
                CountrySeeder::class,
                StateSeeder::class,
                CompanySeeder::class,
                ActivityTypeSeeder::class,
                ActivityPlanSeeder::class,
                UOMCategorySeeder::class,
                UOMSeeder::class,
                UtmStageSeeder::class,
                UtmCampaignSeeder::class,
                UTMMediumSeeder::class,
                UTMSourceSeeder::class,
            ]);
        });
    }
}

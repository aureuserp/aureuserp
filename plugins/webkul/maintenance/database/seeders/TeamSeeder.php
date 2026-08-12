<?php

namespace Webkul\Maintenance\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Maintenance\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::query()->updateOrCreate(
            [
                'name' => 'Internal Maintenance',
            ],
            [
                'creator_id' => User::query()->value('id'),
                'company_id' => Company::query()->value('id'),
            ],
        );
    }
}

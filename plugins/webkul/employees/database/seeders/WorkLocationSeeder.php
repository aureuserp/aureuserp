<?php

namespace Webkul\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class WorkLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees_work_locations')->delete();

        $company = Company::first();

        $user = User::first();

        $workLocations = [
            [
                'name'               => 'Home',
                'company_id'         => $company?->id,
                'location_type'      => 'home',
                'is_active'          => true,
                'creator_id'         => $user?->id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'name'               => 'Building 1, Second Floor',
                'company_id'         => $company?->id,
                'location_type'      => 'office',
                'is_active'          => true,
                'creator_id'         => $user?->id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'name'               => 'Other',
                'company_id'         => $company?->id,
                'location_type'      => 'other',
                'is_active'          => true,
                'creator_id'         => $user?->id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ];

        DB::table('employees_work_locations')->insert($workLocations);
    }
}

<?php

namespace Webkul\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class RecurringPlanSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::find(1);

        $plans = [
            'Monthly'      => 1,
            'Yearly'       => 12,
            'Over 3 years' => 36,
            'Over 5 years' => 60,
        ];

        $sort = 0;

        foreach ($plans as $name => $months) {
            $sort++;

            if (DB::table('crm_recurring_plans')->where('name', $name)->exists()) {
                continue;
            }

            DB::table('crm_recurring_plans')->insert([
                'name'             => $name,
                'number_of_months' => $months,
                'sort'             => $sort,
                'creator_id'       => $creator?->id,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }
}

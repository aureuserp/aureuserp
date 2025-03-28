<?php

namespace Webkul\TimeOff\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class AccrualPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('time_off_leave_accrual_plans')->delete();

        $user = User::first();

        $company = Company::first();

        $leaveAccrualPlans = [
            [
                'company_id'        => $company?->id,
                'creator_id'        => $user?->id,
                'name'              => 'Seniority Plan',
                'transition_mode'   => 'immediately',
                'accrued_gain_time' => 'end',
                'carryover_date'    => 'year_start',
                'carryover_month'   => 'jan',
                'is_active'         => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ];

        DB::table('time_off_leave_accrual_plans')->insert($leaveAccrualPlans);
    }
}

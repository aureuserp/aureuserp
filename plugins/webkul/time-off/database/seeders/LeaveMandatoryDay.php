<?php

namespace Webkul\TimeOff\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class LeaveMandatoryDay extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('time_off_leave_mandatory_days')->delete();

        $user = User::first();

        $leaveMandatoryDays = [
            [
                'company_id' => 1,
                'creator_id' => $user?->id,
                'color'      => '#FF0000',
                'name'       => 'New Year',
                'start_date' => '2022-01-01',
                'end_date'   => '2022-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'creator_id' => $user?->id,
                'color'      => '#FF0000',
                'name'       => 'Christmas',
                'start_date' => '2022-12-25',
                'end_date'   => '2022-12-25',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('time_off_leave_mandatory_days')->insert($leaveMandatoryDays);
    }
}

<?php

namespace Webkul\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Calendar;

class CalendarAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('calendar_attendances')->delete();

        $user = User::first();

        $calendar = Calendar::first();

        $now = now();

        $defaults = [
            'creator_id'   => $user?->id,
            'calendar_id'  => $calendar?->id,
            'sort'         => 10,
            'week_type'    => null,
            'display_type' => null,
            'date_from'    => null,
            'date_to'      => null,
            'created_at'   => $now,
            'updated_at'   => $now,
        ];

        DB::table('calendar_attendances')->insert([
            $defaults + [
                'name'          => 'Monday Morning',
                'day_of_week'   => 'monday',
                'day_period'    => 'morning',
                'hour_from'     => 8,
                'hour_to'       => 12,
                'duration_days' => 0.5,
            ],
            $defaults + [
                'name'          => 'Monday Lunch',
                'day_of_week'   => 'monday',
                'day_period'    => 'lunch',
                'hour_from'     => 12,
                'hour_to'       => 13,
                'duration_days' => 0,
            ],
            $defaults + [
                'name'          => 'Monday Afternoon',
                'day_of_week'   => 'monday',
                'day_period'    => 'afternoon',
                'hour_from'     => 13,
                'hour_to'       => 17,
                'duration_days' => 0.5,
            ],
            $defaults + [
                'name'          => 'Tuesday Morning',
                'day_of_week'   => 'tuesday',
                'day_period'    => 'morning',
                'hour_from'     => 8,
                'hour_to'       => 12,
                'duration_days' => 0.5,
            ],
            $defaults + [
                'name'          => 'Tuesday Lunch',
                'day_of_week'   => 'tuesday',
                'day_period'    => 'lunch',
                'hour_from'     => 12,
                'hour_to'       => 13,
                'duration_days' => 0,
            ],
            $defaults + [
                'name'          => 'Tuesday Afternoon',
                'day_of_week'   => 'tuesday',
                'day_period'    => 'afternoon',
                'hour_from'     => 13,
                'hour_to'       => 17,
                'duration_days' => 0.5,
            ],
            $defaults + [
                'name'          => 'Wednesday Morning',
                'day_of_week'   => 'wednesday',
                'day_period'    => 'morning',
                'hour_from'     => 8,
                'hour_to'       => 12,
                'duration_days' => 0.5,
            ],
            $defaults + [
                'name'          => 'Wednesday Lunch',
                'day_of_week'   => 'wednesday',
                'day_period'    => 'lunch',
                'hour_from'     => 12,
                'hour_to'       => 13,
                'duration_days' => 0,
            ],
            $defaults + [
                'name'          => 'Wednesday Afternoon',
                'day_of_week'   => 'wednesday',
                'day_period'    => 'afternoon',
                'hour_from'     => 13,
                'hour_to'       => 17,
                'duration_days' => 0.5,
            ],
            $defaults + [
                'name'          => 'Thursday Morning',
                'day_of_week'   => 'thursday',
                'day_period'    => 'morning',
                'hour_from'     => 8,
                'hour_to'       => 12,
                'duration_days' => 0.5,
            ],
            $defaults + [
                'name'          => 'Thursday Lunch',
                'day_of_week'   => 'thursday',
                'day_period'    => 'lunch',
                'hour_from'     => 12,
                'hour_to'       => 13,
                'duration_days' => 0,
            ],
            $defaults + [
                'name'          => 'Thursday Afternoon',
                'day_of_week'   => 'thursday',
                'day_period'    => 'afternoon',
                'hour_from'     => 13,
                'hour_to'       => 17,
                'duration_days' => 0.5,
            ],
            $defaults + [
                'name'          => 'Friday Morning',
                'day_of_week'   => 'friday',
                'day_period'    => 'morning',
                'hour_from'     => 8,
                'hour_to'       => 12,
                'duration_days' => 0.5,
            ],
            $defaults + [
                'name'          => 'Friday Lunch',
                'day_of_week'   => 'friday',
                'day_period'    => 'lunch',
                'hour_from'     => 12,
                'hour_to'       => 13,
                'duration_days' => 0,
            ],
            $defaults + [
                'name'          => 'Friday Afternoon',
                'day_of_week'   => 'friday',
                'day_period'    => 'afternoon',
                'hour_from'     => 13,
                'hour_to'       => 17,
                'duration_days' => 0.5,
            ],
        ]);
    }
}

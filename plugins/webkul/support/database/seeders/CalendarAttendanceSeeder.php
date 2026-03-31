<?php

namespace Webkul\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Calendar;

class CalendarAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('calendar_attendances')->delete();

        $user = User::first();

        $calendar = Calendar::first();

        $calendarAttendanceSeeder = [
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Monday Morning', 'day_of_week' => 'monday', 'day_period' => 'morning', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 8, 'hour_to' => 12, 'duration_days' => 0.5],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Monday Lunch', 'day_of_week' => 'monday', 'day_period' => 'lunch', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 12, 'hour_to' => 13, 'duration_days' => 0],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Monday Afternoon', 'day_of_week' => 'monday', 'day_period' => 'afternoon', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 13, 'hour_to' => 16, 'duration_days' => 0.5],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Tuesday Morning', 'day_of_week' => 'tuesday', 'day_period' => 'morning', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 8, 'hour_to' => 12, 'duration_days' => 0.5],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Tuesday Lunch', 'day_of_week' => 'tuesday', 'day_period' => 'lunch', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 12, 'hour_to' => 13, 'duration_days' => 0],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Tuesday Afternoon', 'day_of_week' => 'tuesday', 'day_period' => 'afternoon', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 13, 'hour_to' => 16, 'duration_days' => 0.5],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Wednesday Morning', 'day_of_week' => 'wednesday', 'day_period' => 'morning', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 8, 'hour_to' => 12, 'duration_days' => 0.5],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Wednesday Lunch', 'day_of_week' => 'wednesday', 'day_period' => 'lunch', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 12, 'hour_to' => 13, 'duration_days' => 0],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Wednesday Afternoon', 'day_of_week' => 'wednesday', 'day_period' => 'afternoon', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 13, 'hour_to' => 16, 'duration_days' => 0.5],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Thursday Morning', 'day_of_week' => 'thursday', 'day_period' => 'morning', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 8, 'hour_to' => 12, 'duration_days' => 0.5],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Thursday Lunch', 'day_of_week' => 'thursday', 'day_period' => 'lunch', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 12, 'hour_to' => 13, 'duration_days' => 0],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Thursday Afternoon', 'day_of_week' => 'thursday', 'day_period' => 'afternoon', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 13, 'hour_to' => 16, 'duration_days' => 0.5],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Friday Morning', 'day_of_week' => 'friday', 'day_period' => 'morning', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 8, 'hour_to' => 12, 'duration_days' => 0.5],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Friday Lunch', 'day_of_week' => 'friday', 'day_period' => 'lunch', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 12, 'hour_to' => 13, 'duration_days' => 0],
            ['creator_id' => $user?->id, 'calendar_id' => $calendar?->id, 'sort' => 10, 'name' => 'Friday Afternoon', 'day_of_week' => 'friday', 'day_period' => 'afternoon', 'week_type' => null, 'display_type' => null, 'date_from' => null, 'date_to' => null, 'hour_from' => 13, 'hour_to' => 16, 'duration_days' => 0.5],
        ];

        DB::table('calendar_attendances')->insert($calendarAttendanceSeeder);
    }
}

<?php

namespace Webkul\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('calendars')->delete();

        $user = User::first();

        $employeesCalendars = [
            ['creator_id' => $user?->id, 'name' => 'Standard 38 hours/week', 'full_time_required_hours' => 38, 'hours_per_day' => 7, 'flexible_hours' => false, 'timezone' => 'Europe/Brussels', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'name' => 'Flexible 40 hours/week', 'full_time_required_hours' => 40, 'hours_per_day' => 8, 'flexible_hours' => true, 'timezone' => 'Europe/Brussels', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'name' => 'Standard 35 hours/week', 'full_time_required_hours' => 35, 'hours_per_day' => 7, 'flexible_hours' => false, 'timezone' => 'Europe/Brussels', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'name' => 'Standard 40 hours/week', 'full_time_required_hours' => 40, 'hours_per_day' => 8, 'flexible_hours' => true, 'timezone' => 'UTC', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('calendars')->insert($employeesCalendars);
    }
}

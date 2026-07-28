<?php

namespace Webkul\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class CalendarSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('calendars')->delete();

        $user = User::first();

        DB::table('calendars')->insert([
            [
                'creator_id'               => $user?->id,
                'name'                     => 'Standard 40 hours/week',
                'full_time_required_hours' => 40,
                'hours_per_day'            => 8,
                'flexible_hours'           => true,
                'timezone'                 => 'UTC',
                'is_active'                => true,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
        ]);
    }
}

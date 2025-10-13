<?php

namespace Webkul\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class UtmStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('utm_campaigns')->delete();
        DB::table('utm_stages')->delete();

        $now = now();

        $user = User::first();

        $utmStages = [
            [
                'sort'       => 1,
                'name'       => 'New',
                'creator_id' => $user?->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'sort'       => 2,
                'name'       => 'Schedule',
                'creator_id' => $user?->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'sort'       => 3,
                'name'       => 'Design',
                'creator_id' => $user?->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'sort'       => 3,
                'name'       => 'Sent',
                'creator_id' => $user?->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('utm_stages')->insert($utmStages);
    }
}

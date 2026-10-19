<?php

namespace Webkul\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class StageSeeder extends Seeder
{
    /**
     * Seed the default pipeline stages without duplicating them on reinstall.
     */
    public function run(): void
    {
        $creator = User::find(1);

        $stages = [
            ['sort' => 1, 'name' => 'New', 'is_won' => false],
            ['sort' => 2, 'name' => 'Qualified', 'is_won' => false],
            ['sort' => 3, 'name' => 'Proposition', 'is_won' => false],
            ['sort' => 4, 'name' => 'Won', 'is_won' => true],
        ];

        foreach ($stages as $stage) {
            DB::table('crm_stages')->updateOrInsert(
                ['name' => $stage['name']],
                [
                    'sort'       => $stage['sort'],
                    'is_won'     => $stage['is_won'],
                    'creator_id' => $creator?->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}

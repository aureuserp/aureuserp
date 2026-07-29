<?php

namespace Webkul\Project\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class ProjectStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('projects_project_stages')->delete();

        $user = User::first();

        DB::table('projects_project_stages')->insert([
            [
                'name'       => 'To Do',
                'is_active'  => true,
                'sort'       => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'In Progress',
                'is_active'  => true,
                'sort'       => 2,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Done',
                'is_active'  => true,
                'sort'       => 3,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Cancelled',
                'is_active'  => true,
                'sort'       => 4,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

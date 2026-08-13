<?php

namespace Webkul\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class SkillTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees_skills')->delete();

        DB::table('employees_skill_types')->delete();

        $user = User::first();

        $skillTypes = [
            [
                'id'         => 1,
                'name'       => 'Languages',
                'color'      => 'danger',
                'is_active'  => true,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 2,
                'name'       => 'Soft Skills',
                'color'      => 'success',
                'is_active'  => true,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 3,
                'name'       => 'Programming Languages',
                'color'      => 'warning',
                'is_active'  => true,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 4,
                'name'       => 'IT',
                'color'      => 'info',
                'is_active'  => true,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 5,
                'name'       => 'Marketing',
                'color'      => 'gray',
                'is_active'  => true,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('employees_skill_types')->insert($skillTypes);
    }
}

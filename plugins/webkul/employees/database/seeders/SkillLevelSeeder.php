<?php

namespace Webkul\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class SkillLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees_skill_levels')->delete();

        $user = User::first();

        $skillLevels = [
            ['creator_id' => $user?->id, 'skill_type_id' => 1, 'level' => 10,  'name' => 'A1', 'default_level' => true, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 1, 'level' => 40,  'name' => 'A2', 'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 1, 'level' => 60,  'name' => 'B1', 'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 1, 'level' => 75,  'name' => 'B2', 'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 1, 'level' => 85,  'name' => 'C1', 'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 1, 'level' => 100, 'name' => 'C2', 'default_level' => null, 'created_at' => now(), 'updated_at' => now()],

            ['creator_id' => $user?->id, 'skill_type_id' => 2, 'level' => 15,  'name' => 'Beginner',      'default_level' => true, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 2, 'level' => 25,  'name' => 'Elementary',    'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 2, 'level' => 50,  'name' => 'Intermediate',  'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 2, 'level' => 80,  'name' => 'Advanced',      'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 2, 'level' => 100, 'name' => 'Expert',        'default_level' => null, 'created_at' => now(), 'updated_at' => now()],

            ['creator_id' => $user?->id, 'skill_type_id' => 3, 'level' => 15,  'name' => 'Beginner',      'default_level' => true, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 3, 'level' => 25,  'name' => 'Elementary',    'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 3, 'level' => 50,  'name' => 'Intermediate',  'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 3, 'level' => 80,  'name' => 'Advanced',      'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 3, 'level' => 100, 'name' => 'Expert',        'default_level' => null, 'created_at' => now(), 'updated_at' => now()],

            ['creator_id' => $user?->id, 'skill_type_id' => 5, 'level' => 25,  'name' => 'L1', 'default_level' => true, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 5, 'level' => 50,  'name' => 'L2', 'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 5, 'level' => 75,  'name' => 'L3', 'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 5, 'level' => 100, 'name' => 'L4', 'default_level' => null, 'created_at' => now(), 'updated_at' => now()],

            ['creator_id' => $user?->id, 'skill_type_id' => 4, 'level' => 15,  'name' => 'Beginner',      'default_level' => true, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 4, 'level' => 25,  'name' => 'Elementary',    'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 4, 'level' => 50,  'name' => 'Intermediate',  'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 4, 'level' => 80,  'name' => 'Advanced',      'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'skill_type_id' => 4, 'level' => 100, 'name' => 'Expert',        'default_level' => null, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('employees_skill_levels')->insert($skillLevels);
    }
}

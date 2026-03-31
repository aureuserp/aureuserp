<?php

namespace Webkul\Employee\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        $this->call([
            EmploymentTypeSeeder::class,
            EmployeeJobPositionSeeder::class,
            SkillTypeSeeder::class,
            SkillSeeder::class,
            SkillLevelSeeder::class,
            WorkLocationSeeder::class,
            EmployeeCategorySeeder::class,
            DepartureReasonSeeder::class,
            ActivityPlanTemplateSeeder::class,
            EmployeeSeeder::class,
            DepartmentSeeder::class,
        ]);
    }
}

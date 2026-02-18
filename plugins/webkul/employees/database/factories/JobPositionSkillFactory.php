<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\EmployeeJobPosition;
use Webkul\Employee\Models\JobPositionSkill;
use Webkul\Employee\Models\Skill;

/**
 * @extends Factory<JobPositionSkill>
 */
class JobPositionSkillFactory extends Factory
{
    protected $model = JobPositionSkill::class;

    public function definition(): array
    {
        return [
            'job_position_id' => EmployeeJobPosition::factory(),
            'skill_id'        => Skill::factory(),
        ];
    }
}

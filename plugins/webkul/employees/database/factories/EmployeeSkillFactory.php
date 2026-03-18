<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmployeeSkill;
use Webkul\Employee\Models\Skill;
use Webkul\Employee\Models\SkillLevel;

class EmployeeSkillFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeSkill::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id'    => Employee::factory(),
            'skill_id'       => Skill::factory(),
            'skill_level_id' => SkillLevel::factory(),
            'start_date'     => fake()->date(),
            'notes'          => fake()->text(),
        ];
    }
}

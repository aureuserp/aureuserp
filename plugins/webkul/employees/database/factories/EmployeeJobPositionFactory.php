<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\EmployeeJobPosition;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class EmployeeJobPositionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeJobPosition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'               => fake()->randomNumber(),
            'name'               => fake()->word,
            'description'        => fake()->text,
            'requirements'       => fake()->text,
            'expected_employees' => fake()->randomNumber(),
            'no_of_employee'     => fake()->randomNumber(),
            'status'             => true,
            'no_of_recruitment'  => fake()->randomNumber(),
            'department_id'      => Department::factory(),
            'company_id'         => Company::factory(),
            'open_date'          => fake()->date(),
            'creator_id'         => User::query()->value('id') ?? User::factory(),
        ];
    }
}

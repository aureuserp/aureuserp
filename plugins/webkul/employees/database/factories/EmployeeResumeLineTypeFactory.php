<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\EmployeeResumeLineType;
use Webkul\Security\Models\User;

/**
 * @extends Factory<EmployeeResumeLineType>
 */
class EmployeeResumeLineTypeFactory extends Factory
{
    protected $model = EmployeeResumeLineType::class;

    public function definition(): array
    {
        return [
            'sort'       => fake()->numberBetween(1, 100),
            'name'       => fake()->words(2, true),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }
}

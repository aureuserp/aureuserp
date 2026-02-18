<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Recruitment\Models\Degree;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Degree>
 */
class DegreeFactory extends Factory
{
    protected $model = Degree::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Bachelor', 'Master', 'PhD', 'High School', 'Associate']),
            'sort' => 1,
            'creator_id' => User::factory(),
        ];
    }
}

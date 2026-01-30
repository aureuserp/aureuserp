<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Recruitment\Models\ApplicantCategory;
use Webkul\Security\Models\User;

/**
 * @extends Factory<ApplicantCategory>
 */
class ApplicantCategoryFactory extends Factory
{
    protected $model = ApplicantCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'color' => fake()->hexColor(),
            'creator_id' => User::factory(),
        ];
    }
}

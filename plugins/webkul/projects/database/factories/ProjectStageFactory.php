<?php

namespace Webkul\Project\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Project\Models\ProjectStage;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<ProjectStage>
 */
class ProjectStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProjectStage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string => , mixed>
     */
    public function definition(): array
    {
        return [
            'name'         => fake()->name(),
            'sort'         => fake()->randomNumber(),
            'is_active'    => true,
            'is_collapsed' => false,
            'company_id'   => Company::factory(),
            'creator_id'   => User::factory(),
        ];
    }
}

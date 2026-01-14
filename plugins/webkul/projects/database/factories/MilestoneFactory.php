<?php

namespace Webkul\Project\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Tag;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Tag>
 */
class MilestoneFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'         => fake()->name(),
            'deadline'     => fake()->date(),
            'is_completed' => fake()->boolean(),
            'completed_at' => fake()->date(),
            'project_id'   => Project::factory(),
            'creator_id'   => User::factory(),
        ];
    }
}

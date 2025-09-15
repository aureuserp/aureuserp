<?php

namespace Webkul\Project\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string => , mixed>
     */
    public function definition(): array
    {
        return [
            'title'               => fake()->name(),
            'description'         => fake()->sentence(),
            'visibility'          => 'public',
            'color'               => fake()->hexColor(),
            'priority'            => fake()->randomNumber(),
            'state'               => 'in_progress',
            'sort'                => fake()->randomNumber(),
            'deadline'            => fake()->date(),
            'is_active'           => true,
            'is_recurring'        => false,
            'working_hours_open'  => 0,
            'working_hours_close' => 0,
            'allocated_hours'     => $hours = fake()->randomNumber(),
            'effective_hours'     => 0,
            'remaining_hours'     => $hours,
            'total_hours_spent'   => 0,
            'overtime'            => 0,
            'progress'            => 0,
            'parent_id'           => Task::factory(),
            'project_id'          => Project::factory(),
            'stage_id'            => TaskStage::factory(),
            'partner_id'          => Partner::factory(),
            'company_id'          => Company::factory(),
            'creator_id'          => User::factory(),
        ];
    }
}

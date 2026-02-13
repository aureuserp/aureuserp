<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Recruitment\Models\JobPosition;
use Webkul\Recruitment\Models\JobPositionInterviewer;
use Webkul\Security\Models\User;

/**
 * @extends Factory<JobPositionInterviewer>
 */
class JobPositionInterviewerFactory extends Factory
{
    protected $model = JobPositionInterviewer::class;

    public function definition(): array
    {
        return [
            'job_position_id' => JobPosition::factory(),
            'user_id' => User::factory(),
        ];
    }
}

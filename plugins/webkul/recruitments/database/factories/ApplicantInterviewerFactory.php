<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Recruitment\Models\ApplicantInterviewer;
use Webkul\Security\Models\User;

/**
 * @extends Factory<ApplicantInterviewer>
 */
class ApplicantInterviewerFactory extends Factory
{
    protected $model = ApplicantInterviewer::class;

    public function definition(): array
    {
        return [
            'applicant_id' => Applicant::factory(),
            'interviewer_id' => User::factory(),
        ];
    }
}

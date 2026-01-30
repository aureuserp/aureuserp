<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Department;
use Webkul\Recruitment\Enums\ApplicationStatus;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Recruitment\Models\Candidate;
use Webkul\Recruitment\Models\JobPosition;
use Webkul\Recruitment\Models\RefuseReason;
use Webkul\Recruitment\Models\Stage;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UTMMedium;
use Webkul\Support\Models\UTMSource;

/**
 * @extends Factory<Applicant>
 */
class ApplicantFactory extends Factory
{
    protected $model = Applicant::class;

    public function definition(): array
    {
        return [
            'state'                   => ApplicationStatus::ONGOING,
            'priority'                => 0,
            'is_active'               => true,
            'probability'             => 0,
            'salary_proposed'         => 0,
            'salary_expected'         => 0,
            'delay_close'             => 0,
            'email_cc'                => null,
            'salary_proposed_extra'   => null,
            'salary_expected_extra'   => null,
            'applicant_properties'    => null,
            'applicant_notes'         => null,
            'create_date'             => now(),
            'date_opened'             => null,
            'date_closed'             => null,
            'date_last_stage_updated' => null,
            'refuse_date'             => null,

            // Relationships
            'candidate_id'     => Candidate::factory(),
            'stage_id'         => Stage::factory(),
            'last_stage_id'    => null,
            'company_id'       => Company::factory(),
            'recruiter_id'     => User::factory(),
            'job_id'           => JobPosition::factory(),
            'department_id'    => Department::factory(),
            'refuse_reason_id' => null,
            'creator_id'       => User::factory(),
            'source_id'        => null,
            'medium_id'        => null,
        ];
    }

    public function hired(): static
    {
        return $this->state(fn (array $attributes) => [
            'state'       => ApplicationStatus::HIRED,
            'probability' => 100,
            'date_closed' => now(),
        ]);
    }

    public function contract(): static
    {
        return $this->state(fn (array $attributes) => [
            'state'       => ApplicationStatus::ONGOING,
            'probability' => 80,
        ]);
    }

    public function refused(): static
    {
        return $this->state(fn (array $attributes) => [
            'state'            => ApplicationStatus::REFUSED,
            'is_active'        => false,
            'refuse_date'      => now(),
            'refuse_reason_id' => RefuseReason::factory(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withUTM(): static
    {
        return $this->state(fn (array $attributes) => [
            'source_id' => UTMSource::factory(),
            'medium_id' => UTMMedium::factory(),
        ]);
    }

    public function withSalary(): static
    {
        return $this->state(fn (array $attributes) => [
            'salary_proposed' => fake()->randomFloat(2, 30000, 150000),
            'salary_expected' => fake()->randomFloat(2, 35000, 160000),
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 3,
        ]);
    }
}

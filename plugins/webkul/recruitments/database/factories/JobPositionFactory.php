<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Database\Factories\EmployeeJobPositionFactory;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Industry;
use Webkul\Partner\Models\Partner;
use Webkul\Recruitment\Models\JobPosition;

/**
 * @extends Factory<JobPosition>
 */
class JobPositionFactory extends EmployeeJobPositionFactory
{
    protected $model = JobPosition::class;

    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'address_id' => null,
            'manager_id' => null,
            'industry_id' => null,
            'recruiter_id' => null,
            'no_of_hired_employee' => 0,
            'date_from' => null,
            'date_to' => null,
        ]);
    }

    public function withAddress(): static
    {
        return $this->state(fn (array $attributes) => [
            'address_id' => Partner::factory()->state(['sub_type' => 'company']),
        ]);
    }

    public function withManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'manager_id' => Employee::factory(),
        ]);
    }

    public function withIndustry(): static
    {
        return $this->state(fn (array $attributes) => [
            'industry_id' => Industry::factory(),
        ]);
    }

    public function withRecruiter(): static
    {
        return $this->state(fn (array $attributes) => [
            'recruiter_id' => Employee::factory(),
        ]);
    }

    public function withDates(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_from' => now(),
            'date_to' => now()->addMonths(6),
        ]);
    }

    public function hired(int $count = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'no_of_hired_employee' => $count,
        ]);
    }
}

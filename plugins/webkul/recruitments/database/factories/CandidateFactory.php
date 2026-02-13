<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Recruitment\Models\Candidate;
use Webkul\Recruitment\Models\Degree;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Candidate>
 */
class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email_from' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'priority' => 0,
            'is_active' => true,
            'message_bounced' => false,
            'linkedin_profile' => null,
            'email_cc' => null,
            'availability_date' => null,
            'candidate_properties' => null,

            // Relationships
            'company_id' => Company::factory(),
            'partner_id' => Partner::factory(),
            'degree_id' => Degree::factory(),
            'manager_id' => null,
            'employee_id' => null,
            'creator_id' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function bounced(): static
    {
        return $this->state(fn (array $attributes) => [
            'message_bounced' => true,
        ]);
    }

    public function withLinkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'linkedin_profile' => fake()->url(),
        ]);
    }

    public function withManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'manager_id' => User::factory(),
        ]);
    }

    public function withEmployee(): static
    {
        return $this->state(fn (array $attributes) => [
            'employee_id' => Employee::factory(),
        ]);
    }

    public function withAvailability(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_date' => fake()->date(),
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 3,
        ]);
    }
}

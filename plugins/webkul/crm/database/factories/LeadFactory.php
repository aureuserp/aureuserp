<?php

namespace Webkul\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

class LeadFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'type'                  => LeadType::OPPORTUNITY,
            'name'                  => fake()->catchPhrase(),
            'email'                 => fake()->safeEmail(),
            'phone'                 => fake()->phoneNumber(),
            'expected_revenue'      => fake()->randomFloat(2, 500, 50000),
            'probability'           => fake()->numberBetween(10, 90),
            'priority'              => fake()->randomElement(OpportunityPriority::cases()),
            'expected_closing_date' => fake()->dateTimeBetween('now', '+3 months'),
            'stage_id'              => Stage::query()->where('is_won', false)->orderBy('sort')->value('id') ?? Stage::factory(),
            'user_id'               => User::query()->value('id') ?? User::factory(),
            'creator_id'            => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function lead(): static
    {
        return $this->state(fn (): array => [
            'type'         => LeadType::LEAD,
            'company_name' => fake()->company(),
            'contact_name' => fake()->name(),
            'job_title'    => fake()->jobTitle(),
            'website'      => fake()->url(),
            'city'         => fake()->city(),
        ]);
    }
}

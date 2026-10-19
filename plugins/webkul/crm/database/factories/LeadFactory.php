<?php

namespace Webkul\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type'                  => Lead::TYPE_OPPORTUNITY,
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

    /**
     * Indicate that the record is an unqualified lead.
     */
    public function lead(): static
    {
        return $this->state(fn (): array => [
            'type'         => Lead::TYPE_LEAD,
            'company_name' => fake()->company(),
            'contact_name' => fake()->name(),
            'job_title'    => fake()->jobTitle(),
            'website'      => fake()->url(),
            'city'         => fake()->city(),
        ]);
    }
}

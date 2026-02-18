<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityPlanTemplate;
use Webkul\Support\Models\ActivityType;

/**
 * @extends Factory<ActivityPlanTemplate>
 */
class ActivityPlanTemplateFactory extends Factory
{
    protected $model = ActivityPlanTemplate::class;

    public function definition(): array
    {
        return [
            'sort'             => $this->faker->numberBetween(1, 100),
            'summary'          => $this->faker->sentence(),
            'note'             => $this->faker->optional()->paragraph(),
            'delay_count'      => $this->faker->numberBetween(0, 30),
            'delay_unit'       => $this->faker->randomElement(['days', 'weeks', 'months']),
            'delay_from'       => $this->faker->randomElement(['previous_activity', 'begin']),
            'plan_id'          => null,
            'activity_type_id' => ActivityType::factory(),
            'responsible_id'   => null,
            'creator_id'       => User::factory(),
        ];
    }
}

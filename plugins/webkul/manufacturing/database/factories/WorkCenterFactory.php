<?php

namespace Webkul\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Manufacturing\Enums\WorkCenterWorkingState;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<WorkCenter>
 */
class WorkCenterFactory extends Factory
{
    protected $model = WorkCenter::class;

    public function definition(): array
    {
        return [
            'sort'             => fake()->numberBetween(1, 100),
            'color'            => (string) fake()->numberBetween(1, 9),
            'name'             => fake()->company(),
            'code'             => strtoupper(fake()->lexify('WC???')),
            'working_state'    => WorkCenterWorkingState::NORMAL,
            'note'             => fake()->sentence(),
            'time_efficiency'  => fake()->randomFloat(2, 75, 100),
            'default_capacity' => fake()->numberBetween(1, 10),
            'costs_per_hour'   => fake()->randomFloat(4, 10, 250),
            'setup_time'       => fake()->randomFloat(4, 0, 60),
            'cleanup_time'     => fake()->randomFloat(4, 0, 60),
            'oee_target'       => fake()->randomFloat(2, 50, 95),
            'company_id'       => Company::query()->value('id') ?? Company::factory(),
            'calendar_id'      => Calendar::query()->value('id') ?? Calendar::factory(),
            'creator_id'       => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function blocked(): static
    {
        return $this->state(fn () => ['working_state' => WorkCenterWorkingState::BLOCKED]);
    }

    public function costsPerHour(float $amount): static
    {
        return $this->state(fn () => ['costs_per_hour' => $amount]);
    }

    public function setupTimes(float $setup, float $cleanup): static
    {
        return $this->state(fn () => ['setup_time' => $setup, 'cleanup_time' => $cleanup]);
    }

    public function efficiency(float $percent): static
    {
        return $this->state(fn () => ['time_efficiency' => $percent]);
    }

    public function capacity(int $capacity): static
    {
        return $this->state(fn () => ['default_capacity' => $capacity]);
    }
}

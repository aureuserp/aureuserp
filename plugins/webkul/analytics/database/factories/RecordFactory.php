<?php

namespace Webkul\Analytic\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Analytic\Models\Record;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Record>
 */
class RecordFactory extends Factory
{
    protected $model = Record::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['revenue', 'expense', 'hours']),
            'name' => fake()->words(3, true),
            'date' => now(),
            'amount' => 0,
            'unit_amount' => 0,

            // Relationships
            'partner_id' => null,
            'company_id' => Company::factory(),
            'user_id' => null,
            'creator_id' => User::factory(),
        ];
    }

    public function revenue(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'revenue',
            'amount' => fake()->randomFloat(2, 100, 10000),
        ]);
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
            'amount' => fake()->randomFloat(2, 50, 5000),
        ]);
    }

    public function hours(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'hours',
            'unit_amount' => fake()->randomFloat(2, 1, 10),
        ]);
    }

    public function withPartner(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_id' => Partner::factory(),
        ]);
    }

    public function withUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
        ]);
    }
}

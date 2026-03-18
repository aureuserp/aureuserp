<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\WorkLocation;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class WorkLocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id'      => Company::factory(),
            'user_id'         => User::query()->value('id') ?? User::factory(),
            'name'            => fake()->name,
            'location_type'   => fake()->word,
            'location_number' => fake()->numberBetween(1, 100),
            'active'          => 1,
        ];
    }
}

<?php

namespace Webkul\Sale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Sale\Models\Team;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    use HasCompanyDefault;

    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'            => fake()->randomNumber(),
            'user_id'         => null,
            'color'           => fake()->hexColor,
            'creator_id'      => null,
            'name'            => fake()->name,
            'is_active'       => fake()->boolean,
            'invoiced_target' => fake()->randomNumber(),
        ];
    }
}

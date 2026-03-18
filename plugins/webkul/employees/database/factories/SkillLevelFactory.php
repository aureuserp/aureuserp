<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\SkillLevel;
use Webkul\Employee\Models\SkillType;

class SkillLevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SkillLevel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'          => fake()->name,
            'skill_type_id' => SkillType::factory(),
            'level'         => fake()->numberBetween(5, 100),
            'default_level' => 0,
        ];
    }
}
